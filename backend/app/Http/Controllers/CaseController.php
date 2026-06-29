<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use App\Models\ActivityLog;
use App\Models\Verification;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    /**
     * List all cases with filters.
     */
    public function index(Request $request)
    {
        $query = Hazard::with('creator');

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('location')) {
            $query->where('location_name', 'like', '%' . $request->location . '%');
        }

        $hazards = $query->orderBy('created_at', 'desc')->get();
        return view('admin.cases.index', compact('hazards'));
    }

    /**
     * Display a specific case.
     */
    public function show($id)
    {
        $hazard = Hazard::with(['creator', 'verifications.user', 'aiLogs'])->findOrFail($id);
        return view('admin.cases.show', compact('hazard'));
    }

    /**
     * Verify a case report.
     */
    public function verify($id)
    {
        $hazard = Hazard::findOrFail($id);
        $hazard->status = 'Verified';
        $hazard->increment('verification_count');
        $hazard->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Case Verified',
            'description' => "Verified hazard case #{$hazard->id} ({$hazard->category} at {$hazard->location_name}).",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Case verified successfully!');
    }

    /**
     * Reject a case report (False Report).
     */
    public function reject($id)
    {
        $hazard = Hazard::findOrFail($id);
        $hazard->status = 'Rejected';
        $hazard->increment('false_report_count');
        $hazard->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Case Rejected',
            'description' => "Rejected hazard case #{$hazard->id} as false report.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Case rejected and flagged as false report.');
    }

    /**
     * Mark a case as resolved.
     */
    public function resolve($id)
    {
        $hazard = Hazard::findOrFail($id);
        $hazard->status = 'Resolved';
        $hazard->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Case Resolved',
            'description' => "Marked hazard case #{$hazard->id} as resolved.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Case resolved successfully!');
    }

    /**
     * Archive a case.
     */
    public function archive($id)
    {
        $hazard = Hazard::findOrFail($id);
        $hazard->is_archived = true;
        $hazard->status = 'Archived';
        $hazard->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Case Archived',
            'description' => "Archived hazard case #{$hazard->id}.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->route('admin.cases.index')->with('success', 'Case archived successfully!');
    }

    /**
     * Delete a case.
     */
    public function destroy($id)
    {
        $hazard = Hazard::findOrFail($id);
        $hazard->delete();

        return redirect()->route('admin.cases.index')->with('success', 'Case deleted successfully!');
    }

    /**
     * Upload an evidence image for a past incident / hazard case directly to GCP Cloud Storage.
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $hazard = Hazard::findOrFail($id);
        $file = $request->file('image');
        $filename = 'hazard_' . $hazard->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $mime = $file->getClientMimeType() ?: 'image/jpeg';

        // Upload to GCP Cloud Storage via GcsService
        $gcs = new \App\Services\GcsService();
        $url = $gcs->uploadImage(file_get_contents($file->getRealPath()), $filename, $mime);

        if (!$url) {
            // Local fallback if GCP credentials absent
            $path = $file->storeAs('uploads/hazards', $filename, 'public');
            $url = 'uploads/hazards/' . $filename;
        }

        // Append to existing comma-separated images or set initial
        if (!empty($hazard->image_path)) {
            $hazard->image_path = $hazard->image_path . ',' . $url;
        } else {
            $hazard->image_path = $url;
        }
        $hazard->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'type' => 'Admin',
            'action' => 'Incident Image Uploaded',
            'description' => "Uploaded new evidence photo for incident case #{$hazard->id} ({$hazard->category}).",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        return redirect()->back()->with('success', 'Incident photo uploaded successfully!');
    }
}
