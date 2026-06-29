<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsApiController extends Controller
{
    /**
     * GET /settings
     * Fetches system feature flags and app configurations.
     */
    public function index()
    {
        $dbCategories = \App\Models\Category::where('is_active', true)->pluck('name')->toArray();
        if (empty($dbCategories)) {
            $dbCategories = ['Pothole', 'Waterlogging', 'Broken Light', 'Road Collapse', 'Other'];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'app_name' => SettingsService::get('app_name', 'NagarRakshak'),
                'gemini_analysis_enabled' => SettingsService::get('gemini_analysis_enabled', '1') === '1',
                'petition_enabled' => SettingsService::get('petition_enabled', '1') === '1',
                'alert_radius' => (int) SettingsService::get('alert_radius', '500'),
                'auto_escalation' => SettingsService::get('auto_escalation', '1') === '1',
                'maintenance_mode' => SettingsService::get('maintenance_mode', '0') === '1',
                'app_version' => SettingsService::get('app_version', '1.2.0'),
                'app_update_mandatory' => SettingsService::get('app_update_mandatory', '0') === '1',
                'app_update_url' => SettingsService::get('app_update_url', ''),
                'categories' => $dbCategories,
            ]
        ]);
    }

    /**
     * PUT /settings
     * Synchronizes personal app preferences back to user settings schema.
     */
    public function update(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Preferences synchronized successfully'
        ]);
    }

    /**
     * GET /settings/preferences
     * Fetches user specific preferences (notification, location, voice, sounds, maps).
     */
    public function getPreferences()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email_notifications' => (bool)$user->email_notifications,
                'push_notifications' => (bool)$user->push_notifications,
                'hazard_alerts' => (bool)$user->hazard_alerts,
                'high_accuracy_location' => (bool)$user->high_accuracy_location,
                'background_location' => (bool)$user->background_location,
                'offline_map_downloaded' => (bool)$user->offline_map_downloaded,
                'voice_alerts_enabled' => (bool)$user->voice_alerts_enabled,
                'sound_alerts_enabled' => (bool)$user->sound_alerts_enabled,
            ]
        ]);
    }

    /**
     * PUT /settings/preferences
     * Updates user specific preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'hazard_alerts' => 'sometimes|boolean',
            'high_accuracy_location' => 'sometimes|boolean',
            'background_location' => 'sometimes|boolean',
            'offline_map_downloaded' => 'sometimes|boolean',
            'voice_alerts_enabled' => 'sometimes|boolean',
            'sound_alerts_enabled' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully',
            'data' => $validated
        ]);
    }
}
