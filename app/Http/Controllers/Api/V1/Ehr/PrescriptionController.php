<?php

namespace App\Http\Controllers\Api\V1\Ehr;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function __construct(
        protected PrescriptionService $prescriptionService
    ) {}

    public function store(Request $request, int $consultationId): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
            'is_finalized' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string|max:255',
            'items.*.generic_name' => 'nullable|string|max:255',
            'items.*.form' => 'nullable|string|max:50',
            'items.*.strength' => 'nullable|string|max:100',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.route' => 'nullable|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:500',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        $prescription = $this->prescriptionService->createPrescription(
            $request->user(),
            $consultationId,
            $validated
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحرير الوصفة الطبية الإلكترونية بنجاح.',
            'data' => $prescription,
        ], 201);
    }

    public function finalize(Request $request, int $id): JsonResponse
    {
        $prescription = Prescription::findOrFail($id);

        $finalized = $this->prescriptionService->finalizePrescription(
            $request->user(),
            $prescription
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم اعتماد الوصفة الطبية رسمياً وإصدار الرقم المرجعي.',
            'data' => $finalized,
        ]);
    }

    public function amend(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string|max:255',
            'items.*.generic_name' => 'nullable|string|max:255',
            'items.*.form' => 'nullable|string|max:50',
            'items.*.strength' => 'nullable|string|max:100',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.route' => 'nullable|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:500',
        ]);

        $original = Prescription::findOrFail($id);

        $amended = $this->prescriptionService->amendPrescription(
            $request->user(),
            $original,
            $validated,
            $validated['reason']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إصدار وصفة معدلة وأرشفة الوصفة الأصلية بنجاح.',
            'data' => $amended,
        ]);
    }

    public function patientPrescriptions(Request $request, int $patientId): JsonResponse
    {
        $prescriptions = $this->prescriptionService->getPatientPrescriptions(
            $request->user(),
            $patientId
        );

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }
}
