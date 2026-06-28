<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('aadhaar_number')->nullable();
            $table->boolean('id_card_verified')->default(false);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('hazard_alerts')->default(true);
            $table->boolean('high_accuracy_location')->default(true);
            $table->boolean('background_location')->default(false);
            $table->boolean('offline_map_downloaded')->default(false);
            $table->boolean('voice_alerts_enabled')->default(true);
            $table->boolean('sound_alerts_enabled')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'two_factor_enabled',
                'aadhaar_number',
                'id_card_verified',
                'email_notifications',
                'push_notifications',
                'hazard_alerts',
                'high_accuracy_location',
                'background_location',
                'offline_map_downloaded',
                'voice_alerts_enabled',
                'sound_alerts_enabled'
            ]);
        });
    }
};
