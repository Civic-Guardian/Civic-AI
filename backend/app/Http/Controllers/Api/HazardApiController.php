<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hazard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HazardApiController extends Controller
{
    /**
     * Fetch all hazard reports from database.
     * Automatically seeds the database if empty to ensure initial mockup data is available.
     */
    public function getHazards()
    {
        try {
            if (Hazard::count() === 0) {
                Artisan::call('db:seed');
            }

            $hazards = Hazard::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $hazards
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new hazard report.
     */
    public function storeHazard(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'category' => 'required|string',
                'location_name' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'severity' => 'nullable|string',
                'description' => 'required|string',
                'ai_analysis_summary' => 'nullable|string',
                'image_path' => 'nullable|string'
            ]);

            // Optionally authenticate user from Bearer token (guest submissions have no token)
            $userId = null;
            $authorization = $request->header('Authorization');
            if ($authorization && str_starts_with($authorization, 'Bearer ')) {
                $token = substr($authorization, 7);
                if (!empty($token)) {
                    $user = \App\Models\User::where('remember_token', $token)->first();
                    if ($user) {
                        $userId = $user->id;
                        $user->increment('reports_submitted');
                        $user->addReputationPoints(100);
                    }
                }
            }

            $hazard = Hazard::create([
                'category' => $validatedData['category'],
                'location_name' => $validatedData['location_name'],
                'latitude' => $validatedData['latitude'],
                'longitude' => $validatedData['longitude'],
                'severity' => $validatedData['severity'] ?? 'Medium Risk',
                'status' => 'Pending',
                'description' => $validatedData['description'],
                'verification_count' => 0,
                'ai_analysis_summary' => $validatedData['ai_analysis_summary'] ?? null,
                'image_path' => $validatedData['image_path'] ?? null,
                'created_by' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'data' => $hazard
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Increment verification count and update status of a hazard.
     */
    public function verifyHazard($id)
    {
        try {
            $hazard = Hazard::findOrFail($id);
            $hazard->increment('verification_count');
            
            if ($hazard->status === 'Pending') {
                $hazard->status = 'Verified';
            }
            $hazard->save();

            // Gamification: Award 20 points and increment verified count
            $verifier = auth()->user();
            if ($verifier) {
                $verifier->increment('reports_verified');
                $verifier->addReputationPoints(20);
            }

            return response()->json([
                'success' => true,
                'data' => $hazard
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hazard not found or verify failed: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mark a hazard as resolved.
     */
    public function resolveHazard($id)
    {
        try {
            $hazard = Hazard::findOrFail($id);
            $hazard->status = 'Resolved';
            $hazard->save();

            // Gamification: Award 150 points to resolver
            $resolver = auth()->user();
            if ($resolver) {
                $resolver->addReputationPoints(150);
            }

            return response()->json([
                'success' => true,
                'data' => $hazard
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hazard not found or resolve failed: ' . $e->getMessage()
            ], 404);
        }
    }
}
