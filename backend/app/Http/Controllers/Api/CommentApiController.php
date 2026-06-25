<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentApiController extends Controller
{
    /**
     * GET /hazards/{id}/comments
     * Retrieve discussion thread comments for a hazard.
     */
    public function index($hazardId)
    {
        // TODO: Retrieve comments for the given hazard ID
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * POST /hazards/{id}/comments
     * Post a new comment.
     */
    public function store(Request $request, $hazardId)
    {
        // TODO: Save the comment for the given hazard
        return response()->json([
            'success' => true,
            'data' => [
                'id' => null,
                'hazard_id' => $hazardId,
                'user' => null,
                'content' => $request->input('content'),
                'created_at' => now()->toIso8601String()
            ]
        ], 201);
    }

    /**
     * DELETE /comments/{id}
     * Deletes a specific comment.
     */
    public function destroy($id)
    {
        // TODO: Delete the specified comment
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
