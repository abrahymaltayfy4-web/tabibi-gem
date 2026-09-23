<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecommendationRule;
use App\Models\SafetyEventLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRecommendationRulesController extends Controller
{
    public function rules(): JsonResponse
    {
        $rules = RecommendationRule::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $rules,
        ]);
    }

    public function storeRule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'symptom_keyword' => 'required|string|max:100',
            'specialty_code' => 'required|string|max:50',
            'weight' => 'nullable|numeric|between:0.1,5.0',
            'is_active' => 'nullable|boolean',
        ]);

        $rule = RecommendationRule::create([
            'symptom_keyword' => $validated['symptom_keyword'],
            'specialty_code' => $validated['specialty_code'],
            'weight' => $validated['weight'] ?? 1.0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة قاعدة التوصيات بنجاح.',
            'data' => $rule,
        ], 201);
    }

    public function safetyLogs(): JsonResponse
    {
        $logs = SafetyEventLog::with('actor')
            ->orderBy('id', 'desc')
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
