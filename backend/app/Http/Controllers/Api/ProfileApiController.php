<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    /**
     * GET /profile/stats
     * Compiles total reports, verification count, reputation points, badges, and user impact metrics.
     */
    public function stats()
    {
        // TODO: Fetch logged user contribution statistics
        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => null,
                'name' => null,
                'reputation_points' => 0,
                'stats' => [
                    'hazards_reported' => 0,
                    'hazards_verified' => 0,
                    'hazards_resolved' => 0
                ]
            ]
        ]);
    }

    /**
     * PUT /profile
     * Updates name, avatar icon, phone, and target preferences.
     */
    public function update(Request $request)
    {
        // TODO: Update user profile
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }
}
