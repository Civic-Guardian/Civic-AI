<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AiApiController extends Controller
{
    /**
     * POST /ai/analyze
     * Processes raw hazard photo via Gemini to detect issue details.
     */
    public function analyze(Request $request, \App\Services\GeminiService $geminiService, \App\Services\GcsService $gcsService)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'description' => 'nullable|string',
            'city' => 'nullable|string',
            'user_name' => 'nullable|string',
        ]);

        try {
            $imageFile = $request->file('image');
            $mimeType = $imageFile->getMimeType();
            
            // Encode image directly to base64
            $imageBase64 = base64_encode(file_get_contents($imageFile->getRealPath()));
            
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $description = $request->input('description');
            $city = $request->input('city');
            $userName = $request->input('user_name');

            // Upload original image directly to GCS (no re-encoding to avoid quality loss and color shifts)
            $originalData = file_get_contents($imageFile->getRealPath());
            $mimeTypeLower = strtolower($mimeType);
            
            // Determine file extension from MIME type
            $extMap = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
            ];
            $ext = $extMap[$mimeTypeLower] ?? 'jpg';
            $fileName = uniqid('hazard_') . '.' . $ext;
            
            $imageUrl = $gcsService->uploadImage($originalData, $fileName, $mimeType);

            if (!$imageUrl) {
                // Save locally as fallback
                $localPath = $imageFile->store('hazards', 'public');
                $imageUrl = asset('storage/' . $localPath);
            }

            // Check if AI analysis is enabled
            $geminiEnabled = \App\Services\SettingsService::get('gemini_analysis_enabled', '1') === '1';
            $petitionEnabled = \App\Services\SettingsService::get('petition_enabled', '1') === '1';

            $analysisResult = [];
            $aiAnalysis = null;

            if ($geminiEnabled) {
                // Call Gemini Service
                $analysisResult = $geminiService->analyzeHazardImage($imageBase64, $mimeType, $latitude, $longitude, $description, $city, $userName);

                // Strip petition if feature is disabled
                if (!$petitionEnabled) {
                    unset($analysisResult['petition_draft']);
                }

                // Store in database
                $aiAnalysis = \App\Models\AiAnalysis::create([
                    'predicted_severity' => $analysisResult['predicted_severity'] ?? null,
                    'generated_summary' => $analysisResult['generated_summary'] ?? null,
                    'petition_draft' => $petitionEnabled ? ($analysisResult['petition_draft'] ?? null) : null,
                    'raw_payload' => $analysisResult,
                ]);
            }

            // Duplicate Detection Logic (20 meters)
            $similarHazards = [];
            if ($latitude && $longitude && isset($analysisResult['predicted_category'])) {
                $category = $analysisResult['predicted_category'];
                
                // Fetch active hazards of the same category
                $potentialHazards = \App\Models\Hazard::where('is_archived', false)
                    ->where('category', $category)
                    ->get();
                
                foreach ($potentialHazards as $hazard) {
                    // Haversine formula in PHP
                    $earthRadius = 6371000; // Radius of earth in meters
                    $latFrom = deg2rad((float)$latitude);
                    $lonFrom = deg2rad((float)$longitude);
                    $latTo = deg2rad((float)$hazard->latitude);
                    $lonTo = deg2rad((float)$hazard->longitude);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                    
                    $distance = $angle * $earthRadius;

                    if ($distance <= 20) {
                        $similarHazards[] = [
                            'id' => $hazard->id,
                            'title' => $hazard->location_name,
                            'severity' => $hazard->severity,
                            'distance_meters' => round($distance, 1),
                            'verification_count' => $hazard->verification_count
                        ];
                    }
                }

                // Sort by distance
                usort($similarHazards, function($a, $b) {
                    return $a['distance_meters'] <=> $b['distance_meters'];
                });
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'ai_analysis_id' => $aiAnalysis?->id,
                    'image_path' => $imageUrl,
                    'gemini_enabled' => $geminiEnabled,
                    'petition_enabled' => $petitionEnabled,
                    'predicted_category' => $analysisResult['predicted_category'] ?? null,
                    'predicted_severity' => $analysisResult['predicted_severity'] ?? null,
                    'confidence_score' => $analysisResult['confidence_score'] ?? null,
                    'generated_summary' => $analysisResult['generated_summary'] ?? null,
                    'petition_draft' => $petitionEnabled ? ($analysisResult['petition_draft'] ?? null) : null,
                    'similar_hazards' => $similarHazards
                ]
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AI Analysis Endpoint Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process AI analysis.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /ai/regenerate-description
     * Regenerates description and petition letters with additional user input context.
     */
    public function regenerateDescription(Request $request)
    {
        // TODO: Implement logic to regenerate description
        return response()->json([
            'success' => true,
            'data' => [
                'generated_summary' => null,
                'petition_draft' => null
            ]
        ]);
    }
}
