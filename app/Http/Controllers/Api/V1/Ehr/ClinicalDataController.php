<?php

namespace App\Http\Controllers\Api\V1\Ehr;

use App\Http\Controllers\Controller;
use App\Models\PatientProfile;
use App\Services\ClinicalDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClinicalDataController extends Controller
{
    public function __construct(
        protected ClinicalDataService $clinicalDataService
    ) {}

    public function profile(Request $request, int $patientId): JsonResponse
    {
        $patient = PatientProfile::findOrFail($patientId);
        if ($request->user()->cannot('view', $patient)) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بالاطلاع على هذا الملف الطبي.',
            ], 403);
        }

        $profile = $this->clinicalDataService->getPatientProfile(
            $request->user(),
            $patientId
        );

        return response()->json([
            'status' => 'success',
            'data' => $profile,
        ]);
    }

    public function storeClinicalNotes(Request $request, int $consultationId): JsonResponse
    {
        $validated = $request->validate([
            'chief_complaint' => 'required|string|max:1000',
            'examination_notes' => 'nullable|string|max:2000',
            'diagnosis_notes' => 'required|string|max:2000',
            'treatment_plan' => 'nullable|string|max:2000',
            'diagnoses' => 'nullable|array',
            'diagnoses.*.diagnosis_name' => 'required_with:diagnoses|string|max:255',
            'diagnoses.*.icd_code' => 'nullable|string|max:50',
            'treatment_plan_details' => 'nullable|array',
            'treatment_plan_details.recommendations' => 'required_with:treatment_plan_details|string|max:1000',
        ]);

        $record = $this->clinicalDataService->createComprehensiveClinicalRecord(
            $request->user(),
            $consultationId,
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم تدوين الملاحظات السريرية الشاملة والتشخيص بنجاح.',
            'data' => $record,
        ], 201);
    }

    public function uploadDocument(Request $request, int $patientId): JsonResponse
    {
        $patient = PatientProfile::findOrFail($patientId);
        if ($request->user()->cannot('view', $patient)) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك برفع مستندات لهذا الملف الطبي.',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|max:15360', // Max 15MB
            'document_type' => 'required|string|in:lab_test,xray,mri,ct,prescription_scan,general_report',
            'medical_record_id' => 'nullable|exists:medical_records,id',
        ]);

        $doc = $this->clinicalDataService->uploadMedicalDocument(
            $request->user(),
            $patientId,
            $request->file('file'),
            $request->input('document_type'),
            $request->input('medical_record_id')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع المستند الطبي وتأطيره أمنياً بنجاح.',
            'data' => $doc,
        ], 201);
    }
}
