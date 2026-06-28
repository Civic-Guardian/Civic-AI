<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hazard;
use App\Models\Bookmark;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileApiController extends Controller
{
    /**
     * GET /profile/stats
     * Compiles total reports, verification count, reputation points, badges, and user impact metrics.
     */
    public function stats()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Fetch counts from database
        $hazardsReported = Hazard::where('created_by', $user->id)->count();
        $hazardsResolved = Hazard::where('created_by', $user->id)->where('status', 'Resolved')->count();
        $hazardsVerified = Verification::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'reputation_score' => $user->reputation_score,
                'badge_level' => $user->badge_level,
                'role' => $user->role,
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
                'aadhaar_number' => $user->aadhaar_number,
                'id_card_verified' => (bool) $user->id_card_verified,
                'stats' => [
                    'hazards_reported' => $hazardsReported,
                    'hazards_verified' => $hazardsVerified,
                    'hazards_resolved' => $hazardsResolved
                ]
            ]
        ]);
    }

    /**
     * GET /profile/reports
     * Returns hazards reported by the current authenticated user.
     */
    public function reports()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $reports = Hazard::where('created_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    /**
     * GET /profile/saved
     * Returns hazards saved/bookmarked by the current authenticated user.
     */
    public function savedAlerts()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $hazardIds = Bookmark::where('user_id', $user->id)->pluck('hazard_id');
        $hazards = Hazard::whereIn('id', $hazardIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $hazards
        ]);
    }

    /**
     * PUT /profile
     * Updates name, email, phone.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * PUT /profile/password
     * Validates and updates password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The old password does not match our records.'
            ], 422);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * PUT /profile/security
     * Toggles/updates 2FA status.
     */
    public function updateSecurity(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'two_factor_enabled' => 'required|boolean'
        ]);

        $user->two_factor_enabled = $validated['two_factor_enabled'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Security settings updated successfully',
            'data' => [
                'two_factor_enabled' => (bool)$user->two_factor_enabled
            ]
        ]);
    }

    /**
     * PUT /profile/verification
     * Updates verification details.
     */
    public function updateVerification(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'aadhaar_number' => 'required|string|min:12|max:12'
        ]);

        $user->aadhaar_number = $validated['aadhaar_number'];
        $user->id_card_verified = true;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Verification details submitted successfully',
            'data' => [
                'aadhaar_number' => $user->aadhaar_number,
                'id_card_verified' => (bool)$user->id_card_verified
            ]
        ]);
    }
}
