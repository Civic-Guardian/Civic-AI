<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $settings = SettingsService::all();

        return view('admin.settings.index', compact('categories', 'settings'));
    }

    /**
     * Update radius threshold and auto settings.
     */
    public function updateAlerts(Request $request)
    {
        $request->validate([
            'alert_radius' => 'required|integer|min:10',
            'critical_threshold' => 'required|integer|min:1',
        ]);

        SettingsService::set('alert_radius', $request->alert_radius);
        SettingsService::set('critical_threshold', $request->critical_threshold);
        SettingsService::set('auto_escalation', $request->has('auto_escalation') ? '1' : '0');
        SettingsService::set('gemini_analysis_enabled', $request->has('gemini_analysis_enabled') ? '1' : '0');
        SettingsService::set('petition_enabled', $request->has('petition_enabled') ? '1' : '0');

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Settings Updated',
            'description' => 'Updated alert configuration thresholds.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Alert settings updated successfully!');
    }

    /**
     * Update system brand details.
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'gemini_api_key' => 'nullable|string',
            'gcs_bucket_name' => 'nullable|string',
            'gcs_key_file' => 'nullable|string',
            'fcm_project_id' => 'nullable|string',
            'fcm_service_account' => 'nullable|string',
        ]);

        SettingsService::set('app_name', $request->app_name);
        SettingsService::set('contact_email', $request->contact_email);
        SettingsService::set('logo_path', $request->logo_path);
        SettingsService::set('google_maps_api_key', $request->google_maps_api_key);
        SettingsService::set('gemini_api_key', $request->gemini_api_key);
        SettingsService::set('gcs_bucket_name', $request->gcs_bucket_name);
        SettingsService::set('gcs_key_file', $request->gcs_key_file);
        SettingsService::set('fcm_project_id', $request->fcm_project_id);
        SettingsService::set('fcm_service_account', $request->fcm_service_account);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Settings Updated',
            'description' => 'Updated core system brand configurations and Maps API.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'System settings saved successfully!');
    }

    /**
     * Add category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories,name',
            'icon' => 'required|string',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active')
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Category Created',
            'description' => "Created new hazard category: '{$request->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    /**
     * Update category.
     */
    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:categories,name,' . $category->id,
            'icon' => 'required|string',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'is_active' => $request->has('is_active')
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Category Updated',
            'description' => "Updated hazard category: '{$request->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category.
     */
    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Category Deleted',
            'description' => "Deleted hazard category: '{$name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }

    /**
     * Update maintenance mode and app update parameters (with GCP APK release upload).
     */
    public function updateMaintenance(Request $request)
    {
        $request->validate([
            'app_version' => 'required|string|max:20',
            'apk_file' => 'nullable|file|max:102400',
            'app_update_url' => 'nullable|string',
        ]);

        SettingsService::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        SettingsService::set('app_version', $request->app_version);
        SettingsService::set('app_update_mandatory', $request->has('app_update_mandatory') ? '1' : '0');

        if ($request->hasFile('apk_file')) {
            try {
                $file = $request->file('apk_file');
                $filename = 'nagarrakshak_v' . str_replace('.', '_', $request->app_version) . '_' . time() . '.apk';
                
                if (config('filesystems.disks.gcs.bucket')) {
                    $path = \Illuminate\Support\Facades\Storage::disk('gcs')->putFileAs('apks', $file, $filename, 'public');
                    $url = \Illuminate\Support\Facades\Storage::disk('gcs')->url($path);
                } else {
                    $path = $file->storeAs('apks', $filename, 'public');
                    $url = asset('storage/' . $path);
                }
                SettingsService::set('app_update_url', $url);
            } catch (\Exception $e) {
                $file = $request->file('apk_file');
                $filename = 'nagarrakshak_v' . str_replace('.', '_', $request->app_version) . '_' . time() . '.apk';
                $path = $file->storeAs('apks', $filename, 'public');
                $url = asset('storage/' . $path);
                SettingsService::set('app_update_url', $url);
            }
        } elseif ($request->filled('app_update_url')) {
            SettingsService::set('app_update_url', $request->app_update_url);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Settings Updated',
            'description' => 'Uploaded new APK release to GCP Storage and updated App Version configurations.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'APK release uploaded to GCP Storage and app update targets saved successfully!');
    }

    /**
     * Update Hackathon Showcase Incidents photos & text on welcome.blade.php
     */
    public function updateShowcase(Request $request)
    {
        for ($i = 1; $i <= 6; $i++) {
            if ($request->filled("showcase_{$i}_title")) {
                SettingsService::set("showcase_{$i}_title", $request->input("showcase_{$i}_title"));
            }
            if ($request->filled("showcase_{$i}_location")) {
                SettingsService::set("showcase_{$i}_location", $request->input("showcase_{$i}_location"));
            }
            if ($request->hasFile("showcase_{$i}_image")) {
                try {
                    $file = $request->file("showcase_{$i}_image");
                    $filename = "showcase_{$i}_" . time() . '.' . $file->getClientOriginalExtension();
                    $mime = $file->getClientMimeType() ?: 'image/jpeg';

                    $gcs = new \App\Services\GcsService();
                    $url = $gcs->uploadImage(file_get_contents($file->getRealPath()), $filename, $mime);

                    if (!$url) {
                        $path = $file->storeAs('uploads/showcase', $filename, 'public');
                        $url = 'uploads/showcase/' . $filename;
                    }
                    SettingsService::set("showcase_{$i}_image", $url);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Failed to upload showcase {$i} image to GCP: " . $e->getMessage());
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Showcase Updated',
            'description' => 'Updated Hackathon homepage showcase images and incident details.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Hackathon showcase incident media and titles updated successfully!');
    }
}
