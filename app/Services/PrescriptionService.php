<?php

namespace App\Services;

use App\Enums\PrescriptionState;
use App\Models\Consultation;
use App\Models\MedicalAccessLog;
use App\Models\Prescription;
use App\Models\PrescriptionAmendment;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PrescriptionService
{
    /**
     * Create draft or finalized e-prescription for consultation by authorized doctor.
     */
    public function createPrescription(User $doctorUser, int $consultationId, array $data): Prescription
    {
        return DB::transaction(function () use ($doctorUser, $consultationId, $data) {
            $consultation = Consultation::findOrFail($consultationId);
            $doctorId = $doctorUser->doctorProfile?->id;

            if ($consultation->doctor_id !== $doctorId) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بتحرير وصفة طبية لهذه الاستشارة.'],
                ]);
            }

            $isFinalizeImmediately = $data['is_finalized'] ?? false;
            $status = $isFinalizeImmediately ? PrescriptionState::Finalized : PrescriptionState::Draft;
            $prescriptionNumber = $isFinalizeImmediately ? $this->generatePrescriptionNumber() : null;

            $prescription = Prescription::create([
                'uuid' => (string) Str::uuid(),
                'prescription_number' => $prescriptionNumber,
                'consultation_id' => $consultationId,
                'doctor_id' => $doctorId,
                'patient_id' => $consultation->patient_id,
                'status' => $status,
                'notes' => $data['notes'] ?? null,
                'issued_at' => now(),
                'expires_at' => now()->addDays(30),
                'finalized_by_user_id' => $isFinalizeImmediately ? $doctorUser->id : null,
                'finalized_at' => $isFinalizeImmediately ? now() : null,
            ]);

            $items = $data['items'] ?? [];
            foreach ($items as $item) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_name' => $item['medication_name'],
                    'generic_name' => $item['generic_name'] ?? null,
                    'form' => $item['form'] ?? 'tablet',
                    'strength' => $item['strength'] ?? null,
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'route' => $item['route'] ?? 'oral',
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'] ?? 1,
                    'instructions' => $item['instructions'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Write Medical Access Audit Log
            MedicalAccessLog::create([
                'actor_id' => $doctorUser->id,
                'patient_id' => $consultation->patient_id,
                'access_context' => 'CREATE_PRESCRIPTION',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);

            return $prescription->load(['items', 'doctor.user']);
        });
    }

    /**
     * Finalize a draft prescription (makes it officially issued and immutable).
     */
    public function finalizePrescription(User $actor, Prescription $prescription): Prescription
    {
        if ($prescription->status !== PrescriptionState::Draft) {
            throw ValidationException::withMessages([
                'status' => ['يمكن اعتماد الوصفات التي في حالة المسودة (Draft) فقط.'],
            ]);
        }

        $prescription->update([
            'status' => PrescriptionState::Finalized,
            'prescription_number' => $this->generatePrescriptionNumber(),
            'finalized_by_user_id' => $actor->id,
            'finalized_at' => now(),
        ]);

        MedicalAccessLog::create([
            'actor_id' => $actor->id,
            'patient_id' => $prescription->patient_id,
            'access_context' => 'FINALIZE_PRESCRIPTION',
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);

        return $prescription->fresh(['items', 'doctor.user']);
    }

    /**
     * Amend a finalized prescription (Spawns a new prescription version, marks original as Amended, records reason).
     */
    public function amendPrescription(User $doctorUser, Prescription $originalPrescription, array $newData, string $reason): Prescription
    {
        if ($originalPrescription->status !== PrescriptionState::Finalized) {
            throw ValidationException::withMessages([
                'status' => ['يمكن تعديل الوصفات الطبية المعتمدة (Finalized) فقط.'],
            ]);
        }

        return DB::transaction(function () use ($doctorUser, $originalPrescription, $newData, $reason) {
            // 1. Create new amended prescription
            $newPrescription = Prescription::create([
                'uuid' => (string) Str::uuid(),
                'prescription_number' => $this->generatePrescriptionNumber(),
                'consultation_id' => $originalPrescription->consultation_id,
                'doctor_id' => $originalPrescription->doctor_id,
                'patient_id' => $originalPrescription->patient_id,
                'status' => PrescriptionState::Finalized,
                'parent_prescription_id' => $originalPrescription->id,
                'amendment_reason' => $reason,
                'notes' => $newData['notes'] ?? $originalPrescription->notes,
                'issued_at' => now(),
                'expires_at' => now()->addDays(30),
                'finalized_by_user_id' => $doctorUser->id,
                'finalized_at' => now(),
            ]);

            $items = $newData['items'] ?? [];
            foreach ($items as $item) {
                PrescriptionItem::create([
                    'prescription_id' => $newPrescription->id,
                    'medication_name' => $item['medication_name'],
                    'generic_name' => $item['generic_name'] ?? null,
                    'form' => $item['form'] ?? 'tablet',
                    'strength' => $item['strength'] ?? null,
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'route' => $item['route'] ?? 'oral',
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'] ?? 1,
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }

            // 2. Snapshot original & update original status to Amended
            PrescriptionAmendment::create([
                'original_prescription_id' => $originalPrescription->id,
                'amended_prescription_id' => $newPrescription->id,
                'author_id' => $doctorUser->id,
                'reason' => $reason,
                'snapshot_json' => $originalPrescription->load('items')->toArray(),
                'created_at' => now(),
            ]);

            $originalPrescription->update([
                'status' => PrescriptionState::Amended,
            ]);

            // Audit log
            MedicalAccessLog::create([
                'actor_id' => $doctorUser->id,
                'patient_id' => $originalPrescription->patient_id,
                'access_context' => 'AMEND_PRESCRIPTION',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);

            return $newPrescription->load(['items', 'doctor.user', 'parentPrescription']);
        });
    }

    /**
     * Get prescriptions for patient.
     */
    public function getPatientPrescriptions(User $actor, int $patientId): array
    {
        // Audit log if accessing another patient's prescriptions
        if ($actor->patientProfile?->id !== $patientId) {
            MedicalAccessLog::create([
                'actor_id' => $actor->id,
                'patient_id' => $patientId,
                'access_context' => 'VIEW_PATIENT_PRESCRIPTIONS',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);
        }

        return Prescription::with(['doctor.user', 'items', 'consultation', 'parentPrescription'])
            ->where('patient_id', $patientId)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    private function generatePrescriptionNumber(): string
    {
        return 'RX-'.date('Ymd').'-'.strtoupper(Str::random(4));
    }
}
