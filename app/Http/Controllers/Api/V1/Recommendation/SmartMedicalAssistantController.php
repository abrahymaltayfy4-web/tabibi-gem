<?php

namespace App\Http\Controllers\Api\V1\Recommendation;

use App\Http\Controllers\Controller;
use App\Models\RecommendationFeedback;
use App\Models\RecommendationRequest;
use App\Services\RecommendationEngineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmartMedicalAssistantController extends Controller
{
    public function __construct(
        protected RecommendationEngineService $recommendationEngine
    ) {}

    public function analyze(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'symptoms_description' => 'required|string|min:3|max:2000',
            'max_price_yer' => 'nullable|numeric|min:0',
            'preferred_gender' => 'nullable|string|in:Male,Female',
            'specialty_code' => 'nullable|string|max:50',
        ]);

        $result = $this->recommendationEngine->getRecommendations(
            $request->user(),
            $validated['symptoms_description'],
            $validated
        );

        return response()->json($result);
    }

    public function feedback(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'is_accepted' => 'required|boolean',
            'selected_doctor_id' => 'nullable|exists:doctor_profiles,id',
            'utility_score' => 'nullable|integer|between:1,5',
            'feedback_notes' => 'nullable|string|max:500',
        ]);

        $recRequest = RecommendationRequest::findOrFail($id);

        $feedback = RecommendationFeedback::create([
            'request_id' => $recRequest->id,
            'patient_id' => $recRequest->patient_id,
            'is_accepted' => $validated['is_accepted'],
            'selected_doctor_id' => $validated['selected_doctor_id'] ?? null,
            'utility_score' => $validated['utility_score'] ?? null,
            'feedback_notes' => $validated['feedback_notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل تقييمك للتوصية بنجاح.',
            'data' => $feedback,
        ], 201);
    }
}
