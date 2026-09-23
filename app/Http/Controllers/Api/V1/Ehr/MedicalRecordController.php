<?php

namespace App\Http\Controllers\Api\V1\Ehr;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function __construct(
        protected MedicalRecordService $medicalRecordService
    ) {}

    public function store(Request $request, int $consultationId): JsonResponse
    {
        $validated = $request->validate([
            'chief_complaint' => 'nullable|string|max:1000',
            'examination_notes' => 'nullable|string|max:2000',
            'diagnosis_notes' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|string|max:2000',
            'is_finalized' => 'nullable|boolean',
            'symptoms' => 'nullable|array',
            'symptoms.*.name' => 'required_with:symptoms|string|max:255',
            'symptoms.*.description' => 'nullable|string|max:500',
            'symptoms.*.severity' => 'nullable|string|in:mild,moderate,severe',
            'symptoms.*.duration' => 'nullable|string|max:100',
            'symptoms.*.onset' => 'nullable|string|max:100',
            'symptoms.*.frequency' => 'nullable|string|max:100',
        ]);

        $record = $this->medicalRecordService->createMedicalRecord(
            $request->user(),
            $consultationId,
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم تدوين السجل الطبي السريري بنجاح.',
            'data' => $record,
        ], 201);
    }

    public function amend(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'change_reason' => 'required|string|max:500',
            'chief_complaint' => 'nullable|string|max:1000',
            'examination_notes' => 'nullable|string|max:2000',
            'diagnosis_notes' => 'nullable|string|max:2000',
            'treatment_plan' => 'nullable|string|max:2000',
        ]);

        $record = MedicalRecord::findOrFail($id);

        $amendedRecord = $this->medicalRecordService->amendMedicalRecord(
            $request->user(),
            $record,
            $validated,
            $validated['change_reason']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعديل السجل الطبي وأرشفة النسخة السابقة بنجاح.',
            'data' => $amendedRecord,
        ]);
    }

    public function history(Request $request, int $patientId): JsonResponse
    {
        $request->validate([
            'access_reason' => 'nullable|string|max:255',
        ]);

        $records = $this->medicalRecordService->getPatientMedicalHistory(
            $request->user(),
            $patientId,
            $request->input('access_reason')
        );

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }
}
