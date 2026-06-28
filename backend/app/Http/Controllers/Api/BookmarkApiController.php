<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkApiController extends Controller
{
    /**
     * POST /hazards/{id}/bookmark
     * Adds hazard report to monitored bookmarks list.
     */
    public function store($hazardId)
    {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        Bookmark::firstOrCreate([
            'user_id' => $userId,
            'hazard_id' => $hazardId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hazard added to bookmarks'
        ]);
    }

    /**
     * DELETE /hazards/{id}/bookmark
     * Removes a hazard from the user's bookmarks list.
     */
    public function destroy($hazardId)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        Bookmark::where('user_id', $userId)
            ->where('hazard_id', $hazardId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hazard removed from bookmarks'
        ]);
    }
}
