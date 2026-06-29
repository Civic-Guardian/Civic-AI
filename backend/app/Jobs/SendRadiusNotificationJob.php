<?php

namespace App\Jobs;

use App\Models\Hazard;
use App\Models\Setting;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRadiusNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $hazard;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Hazard $hazard, $userId = null)
    {
        $this->hazard = $hazard;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseNotificationService $fcmService): void
    {
        try {
            $radiusMeters = (float)(Setting::where('key', 'alert_radius')->value('value') ?? 500);
            $title = "⚠️ Nearby Alert: {$this->hazard->category}";
            $body = "New {$this->hazard->category} reported within {$radiusMeters}m radius at {$this->hazard->location_name}. Proceed with caution!";
            $type = 'Hazard Alert';
            $targetType = "Radius Based ({$radiusMeters}m)";

            $fcmService->send($title, $body, $type, $targetType, "{$radiusMeters}m around {$this->hazard->location_name}", $this->userId);
            
            Log::info("Successfully dispatched radius push notification for hazard ID: {$this->hazard->id}");
        } catch (\Exception $e) {
            Log::error("SendRadiusNotificationJob failed for hazard ID {$this->hazard->id}: " . $e->getMessage());
        }
    }
}
