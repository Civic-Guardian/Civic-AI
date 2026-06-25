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
    public function analyze(Request $request, \App\Services\GeminiService $geminiService)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg|max:10240',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $imageFile = $request->file('image');
            $mimeType = $imageFile->getMimeType();
            
            // Encode image directly to base64
            $imageBase64 = base64_encode(file_get_contents($imageFile->getRealPath()));
            
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            // Call Gemini Service
            $analysisResult = $geminiService->analyzeHazardImage($imageBase64, $mimeType, $latitude, $longitude);

            // Store in database
            $aiAnalysis = \App\Models\AiAnalysis::create([
                'predicted_severity' => $analysisResult['predicted_severity'] ?? null,
                'generated_summary' => $analysisResult['generated_summary'] ?? null,
                'petition_draft' => $analysisResult['petition_draft'] ?? null,
                'raw_payload' => $analysisResult,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'ai_analysis_id' => $aiAnalysis->id,
                    'predicted_category' => $analysisResult['predicted_category'] ?? null,
                    'predicted_severity' => $analysisResult['predicted_severity'] ?? null,
                    'confidence_score' => $analysisResult['confidence_score'] ?? null,
                    'generated_summary' => $analysisResult['generated_summary'] ?? null,
                    'petition_draft' => $analysisResult['petition_draft'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
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
