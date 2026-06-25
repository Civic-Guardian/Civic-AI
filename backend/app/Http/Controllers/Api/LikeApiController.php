<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LikeApiController extends Controller
{
    /**
     * POST /hazards/{id}/like
     * Upvote / verify a hazard. Increments vote count.
     */
    public function store($hazardId)
    {
        // TODO: Implement logic to upvote the hazard
        return response()->json([
            'success' => true,
            'message' => 'Hazard upvoted / verified successfully',
            'verification_count' => 0
        ]);
    }

    /**
     * DELETE /hazards/{id}/like
     * Removes the user's upvote.
     */
    public function destroy($hazardId)
    {
        // TODO: Implement logic to remove upvote from the hazard
        return response()->json([
            'success' => true,
            'message' => 'Upvote removed successfully',
            'verification_count' => 0
        ]);
    }
}
