<?php

namespace App\Http\Controllers\Api\V1\Ehr;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use App\Services\PatientMedicalHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientMedicalHistoryController extends Controller
{
    public function __construct(
        protected PatientMedicalHistoryService $historyService
    ) {}

    public function index(Request $request, int $patientId): JsonResponse
    {
        $patient = PatientProfile::findOrFail($patientId);

        $history = $this->historyService->getPatientHistory(
            $patient,
            $request->user()
        );

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    public function store(Request $request, int $patientId): JsonResponse
    {
        $patient = PatientProfile::findOrFail($patientId);

        $validated = $request->validate([
            'category' => 'required|string|in:chronic_condition,allergy,past_surgery,family_history,past_medication',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'diagnosed_date' => 'nullable|date',
            'status' => 'nullable|string|in:active,resolved,inactive',
        ]);

        $entry = $this->historyService->addHistoryEntry(
            $patient,
            $request->user(),
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة السجل التاريخي بنجاح.',
            'data' => $entry,
        ], 201);
    }
}
