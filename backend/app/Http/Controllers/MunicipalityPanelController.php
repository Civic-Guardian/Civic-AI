<?php

namespace App\Http\Controllers;

use App\Models\Hazard;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MunicipalityPanelController extends Controller
{
    /**
     * Show the Municipal Panel Dashboard.
     */
    public function dashboard()
    {
        $assignedIssues = Hazard::where('status', '!=', 'Resolved')->count();
        $resolvedIssues = Hazard::where('status', 'Resolved')->count();
        $pendingIssues = Hazard::where('status', 'Pending')->count();

        // Calculate ward performance
        $wardPerformance = [];
        $hazards = Hazard::all();
        foreach ($hazards as $hz) {
            $ward = $hz->location_name ?: 'Main City Zone';
            // clean up ward names if long coordinates
            if (str_contains($ward, ',') || strlen($ward) > 40) {
                $ward = 'Ward ' . (1 + (abs(crc32($ward)) % 15));
            }
            if (!isset($wardPerformance[$ward])) {
                $wardPerformance[$ward] = ['total' => 0, 'resolved' => 0, 'avg_time' => '1.5 days'];
            }
            $wardPerformance[$ward]['total']++;
            if ($hz->status === 'Resolved') {
                $wardPerformance[$ward]['resolved']++;
            }
        }
        // take top 5 wards
        $wardPerformance = array_slice($wardPerformance, 0, 5, true);

        $recentCases = Hazard::orderBy('created_at', 'desc')->take(5)->get();

        return view('municipality.dashboard', compact(
            'assignedIssues',
            'resolvedIssues',
            'pendingIssues',
            'wardPerformance',
            'recentCases'
        ));
    }

    /**
     * List all cases assigned to Municipal departments.
     */
    public function indexCases(Request $request)
    {
        $query = Hazard::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $cases = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('municipality.cases', compact('cases'));
    }

    /**
     * Show detailed case details.
     */
    public function showCase($id)
    {
        $case = Hazard::findOrFail($id);
        $comments = $case->comments()->orderBy('created_at', 'desc')->get();

        return view('municipality.show', compact('case', 'comments'));
    }

    /**
     * Handle status updates.
     * Enforces mandatory evidence file upload when status is set to Resolved.
     */
    public function updateStatus(Request $request, $id)
    {
        $case = Hazard::findOrFail($id);

        $rules = [
            'status' => 'required|in:Pending,In Progress,Resolved'
        ];

        // Enforce evidence upload ONLY if status is being updated to Resolved
        if ($request->input('status') === 'Resolved') {
            $rules['evidence_file'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:10240';
        }

        $validated = $request->validate($rules);

        $case->status = $validated['status'];

        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
            $path = $file->store('evidence', 'public');
            
            // Append the uploaded resolution evidence file path to the hazard's image_path
            $case->image_path = empty($case->image_path) ? $path : ($case->image_path . ',' . $path);

            // Add official action comment automatically
            Comment::create([
                'hazard_id' => $case->id,
                'user_name' => 'Municipal Authority (Resolution Team)',
                'content' => 'Marked as RESOLVED. Evidence attached: ' . $file->getClientOriginalName(),
                'is_official' => true
            ]);
        } else {
            // Just normal status change log
            Comment::create([
                'hazard_id' => $case->id,
                'user_name' => 'Municipal Authority',
                'content' => 'Case status updated to: ' . $validated['status'],
                'is_official' => true
            ]);
        }

        $case->save();

        return redirect()->back()->with('success', 'Case status updated successfully.');
    }

    /**
     * Store official comment/response.
     */
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
            'department' => 'nullable|string'
        ]);

        $dept = $request->input('department') ?: 'PWD Division';

        Comment::create([
            'hazard_id' => $id,
            'user_name' => "Municipal Authority ($dept)",
            'content' => $request->input('content'),
            'is_official' => true
        ]);

        return redirect()->back()->with('success', 'Official response posted successfully.');
    }
}
