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
     * Update maintenance mode and app update parameters.
     */
    public function updateMaintenance(Request $request)
    {
        $request->validate([
            'app_version' => 'required|string|max:20',
            'app_update_url' => 'nullable|url',
        ]);

        SettingsService::set('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        SettingsService::set('app_version', $request->app_version);
        SettingsService::set('app_update_mandatory', $request->has('app_update_mandatory') ? '1' : '0');
        SettingsService::set('app_update_url', $request->app_update_url);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Settings Updated',
            'description' => 'Updated Maintenance mode and App Update configuration settings.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Maintenance & App Update configurations saved successfully!');
    }
}
