<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FreshMigrateAndRestore extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:fresh-restore
                            {--force : Skip confirmation prompt}
                            {--skip-cache : Skip session & cache tables during restore}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch all database records, generate a class BackupDataSeeder file, run migrate:fresh, and seed using it.';

    /**
     * Tables to always skip (framework-managed or auto-generated).
     */
    protected array $alwaysSkip = [
        'migrations',
        'failed_jobs',
        'job_batches',
        'jobs',
        'password_reset_tokens',
    ];

    /**
     * Tables skipped when --skip-cache is passed.
     */
    protected array $cacheSkip = [
        'cache',
        'cache_locks',
        'sessions',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // ── Confirmation ──────────────────────────────────────────
        if (! $this->option('force')) {
            $this->warn('⚠️  This will generate BackupDataSeeder, DROP all tables, re-run all migrations, and restore data.');
            if (! $this->confirm('Are you sure you want to proceed?', false)) {
                $this->info('Aborted.');
                return 0;
            }
        }

        $skipCache = $this->option('skip-cache');
        $skipList = array_merge($this->alwaysSkip, $skipCache ? $this->cacheSkip : []);

        // ── Step 1: Discover tables ───────────────────────────────
        $this->info('');
        $this->info('📋 Step 1/4 — Discovering tables...');

        $dbName = DB::getDatabaseName();
        $key = "Tables_in_{$dbName}";
        $allTables = collect(DB::select('SHOW TABLES'))
            ->pluck($key)
            ->toArray();

        $tablesToBackup = array_filter($allTables, function ($table) use ($skipList) {
            return ! in_array($table, $skipList);
        });

        $this->line('   Found ' . count($allTables) . ' tables, backing up ' . count($tablesToBackup) . '.');

        // ── Step 2: Backup data ───────────────────────────────────
        $this->info('');
        $this->info('💾 Step 2/4 — Fetching data from database...');

        $backup = [];
        $totalRows = 0;
        $bar = $this->output->createProgressBar(count($tablesToBackup));
        $bar->start();

        foreach ($tablesToBackup as $table) {
            $rows = DB::table($table)->get()->map(fn ($row) => (array) $row)->toArray();
            $backup[$table] = $rows;
            $totalRows += count($rows);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->line("   Fetched <info>{$totalRows}</info> rows across <info>" . count($tablesToBackup) . "</info> tables.");

        // Show summary table
        $summaryRows = [];
        foreach ($backup as $table => $rows) {
            if (count($rows) > 0) {
                $summaryRows[] = [$table, count($rows)];
            }
        }
        $this->table(['Table', 'Rows'], $summaryRows);

        // ── Step 3: Generate Seeder File ──────────────────────────
        $this->info('');
        $this->info('📝 Step 3/4 — Generating BackupDataSeeder.php seeder class...');
        
        $seederContent = $this->generateSeederCode($backup);
        $seederPath = database_path('seeders/BackupDataSeeder.php');
        
        if (!file_exists(dirname($seederPath))) {
            mkdir(dirname($seederPath), 0755, true);
        }
        
        file_put_contents($seederPath, $seederContent);
        $this->line("   Seeder file written successfully to: <info>{$seederPath}</info>");

        // ── Step 4: Fresh migrate and seed ────────────────────────
        $this->info('');
        $this->info('🔄 Step 4/4 — Running migrate:fresh and seeding via BackupDataSeeder...');

        // Perform migrate:fresh
        $this->call('migrate:fresh', ['--force' => true]);

        // Run the freshly generated seeder
        $this->info('📥 Seeding database with newly generated BackupDataSeeder...');
        $this->call('db:seed', [
            '--class' => 'BackupDataSeeder',
            '--force' => true
        ]);

        $this->newLine();
        $this->info("✅ Done! Database fresh-restored and successfully seeded via BackupDataSeeder.");
        $this->newLine();

        return 0;
    }

    /**
     * Generate the BackupDataSeeder code string dynamically.
     */
    private function generateSeederCode(array $backup): string
    {
        $orderedTables = $this->getInsertOrder($backup);
        $tablesData = [];

        // Sensitive settings keys to redact from seeder
        $sensitiveSettings = [
            'gemini_api_key',
            'google_maps_api_key',
            'fcm_service_account',
            'gcs_key_file'
        ];

        foreach ($orderedTables as $table) {
            $rows = $backup[$table] ?? [];
            if (!empty($rows)) {
                if ($table === 'settings') {
                    $rows = array_map(function ($row) use ($sensitiveSettings) {
                        if (isset($row['key']) && in_array($row['key'], $sensitiveSettings)) {
                            $row['value'] = ''; // Redact key value
                        }
                        return $row;
                    }, $rows);
                }
                $tablesData[$table] = $rows;
            }
        }

        $serializedData = var_export($tablesData, true);

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        \$data = {$serializedData};

        foreach (\$data as \$table => \$rows) {
            if (empty(\$rows)) {
                continue;
            }

            if (Schema::hasTable(\$table)) {
                // Truncate existing data to avoid primary key collisions
                DB::table(\$table)->truncate();

                \$columns = Schema::getColumnListing(\$table);
                \$filteredRows = array_map(function (\$row) use (\$columns) {
                    return array_intersect_key(\$row, array_flip(\$columns));
                }, \$rows);

                foreach (array_chunk(\$filteredRows, 500) as \$chunk) {
                    DB::table(\$table)->insert(\$chunk);
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
PHP;
    }

    /**
     * Simple topological sort based on FK references.
     * Returns table names in safe insert order (parents first).
     */
    private function getInsertOrder(array $backup): array
    {
        $tables = array_keys($backup);

        // Build dependency graph from information_schema
        $dbName = DB::getDatabaseName();
        $fks = DB::select("
            SELECT TABLE_NAME, REFERENCED_TABLE_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName]);

        $deps = [];
        foreach ($tables as $t) {
            $deps[$t] = [];
        }
        foreach ($fks as $fk) {
            $child = $fk->TABLE_NAME;
            $parent = $fk->REFERENCED_TABLE_NAME;
            if (isset($deps[$child]) && in_array($parent, $tables) && $parent !== $child) {
                $deps[$child][] = $parent;
            }
        }

        // Kahn's algorithm
        $inDegree = [];
        foreach ($tables as $t) {
            $inDegree[$t] = 0;
        }
        foreach ($deps as $child => $parents) {
            foreach ($parents as $parent) {
                if (isset($inDegree[$parent])) {
                    $inDegree[$child]++;
                }
            }
        }

        $queue = [];
        foreach ($inDegree as $t => $d) {
            if ($d === 0) {
                $queue[] = $t;
            }
        }

        $sorted = [];
        while (! empty($queue)) {
            $current = array_shift($queue);
            $sorted[] = $current;

            foreach ($deps as $child => $parents) {
                if (in_array($current, $parents)) {
                    $inDegree[$child]--;
                    if ($inDegree[$child] === 0) {
                        $queue[] = $child;
                    }
                }
            }
        }

        // Append any remaining (circular deps) — FK_CHECKS=0 handles them
        foreach ($tables as $t) {
            if (! in_array($t, $sorted)) {
                $sorted[] = $t;
            }
        }

        return $sorted;
    }
}
