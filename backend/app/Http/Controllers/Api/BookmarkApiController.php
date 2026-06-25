<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookmarkApiController extends Controller
{
    /**
     * POST /hazards/{id}/bookmark
     * Adds hazard report to monitored bookmarks list.
     */
    public function store($hazardId)
    {
        // TODO: Implement logic to bookmark the hazard
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
        // TODO: Implement logic to unbookmark the hazard
        return response()->json([
            'success' => true,
            'message' => 'Hazard removed from bookmarks'
        ]);
    }
}
