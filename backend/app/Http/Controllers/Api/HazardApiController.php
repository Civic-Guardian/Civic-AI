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
                    }
                }
            }

            // Duplicate/Merge detection logic (20 meters)
            $matchingHazard = null;
            $latitude = $validatedData['latitude'];
            $longitude = $validatedData['longitude'];
            $category = $validatedData['category'];

            $activeHazards = Hazard::where('category', $category)
                ->where('status', '!=', 'Resolved')
                ->where('is_archived', false)
                ->get();

            foreach ($activeHazards as $hz) {
                $earthRadius = 6371000; // in meters
                $latFrom = deg2rad((float)$latitude);
                $lonFrom = deg2rad((float)$longitude);
                $latTo = deg2rad((float)$hz->latitude);
                $lonTo = deg2rad((float)$hz->longitude);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                
                $distance = $angle * $earthRadius;

                if ($distance <= 20) {
                    $matchingHazard = $hz;
                    break;
                }
            }

            if ($matchingHazard) {
                // Increment verification count (corroborations / evidence)
                $matchingHazard->increment('verification_count');
                
                // Append new image path to list of images if provided and not already present
                if (!empty($validatedData['image_path'])) {
                    $newImg = $validatedData['image_path'];
                    $existingPaths = empty($matchingHazard->image_path) ? [] : explode(',', $matchingHazard->image_path);
                    $existingPaths = array_map('trim', $existingPaths);
                    if (!in_array($newImg, $existingPaths)) {
                        $existingPaths[] = $newImg;
                        $matchingHazard->image_path = implode(',', $existingPaths);
                    }
                }

                if ($matchingHazard->status === 'Pending') {
                    $matchingHazard->status = 'Verified';
                }
                $matchingHazard->save();

                if ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        $user->increment('reports_submitted');
                        $user->addReputationPoints(100);
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => $matchingHazard,
                    'merged' => true
                ], 200);
            }

            // Create new hazard if no duplicate matches
            if ($userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $user->increment('reports_submitted');
                    $user->addReputationPoints(100);
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

            // Execute radius notification dispatch instantly to trigger FCM push alerts & create notification record
            try {
                \App\Jobs\SendRadiusNotificationJob::dispatchSync($hazard, $userId);
            } catch (\Exception $ne) {
                \Illuminate\Support\Facades\Log::error("Failed to dispatch radius notification job: " . $ne->getMessage());
            }

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
