<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\MedicalAccessLog;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordVersion;
use App\Models\Symptom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MedicalRecordService
{
    /**
     * Create medical record for consultation with symptoms and audit logging.
     */
    public function createMedicalRecord(User $doctorUser, int $consultationId, array $data): MedicalRecord
    {
        return DB::transaction(function () use ($doctorUser, $consultationId, $data) {
            $consultation = Consultation::findOrFail($consultationId);
            $doctorId = $doctorUser->doctorProfile?->id;

            if ($consultation->doctor_id !== $doctorId) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بتدوين سجل طبي لهذه الاستشارة.'],
                ]);
            }

            $medicalRecord = MedicalRecord::updateOrCreate(
                ['consultation_id' => $consultationId],
                [
                    'uuid' => (string) Str::uuid(),
                    'patient_id' => $consultation->patient_id,
                    'doctor_id' => $doctorId,
                    'chief_complaint' => $data['chief_complaint'] ?? '',
                    'examination_notes' => $data['examination_notes'] ?? null,
                    'diagnosis_notes' => $data['diagnosis_notes'] ?? '',
                    'treatment_plan' => $data['treatment_plan'] ?? null,
                    'is_finalized' => $data['is_finalized'] ?? true,
                ]
            );

            // Handle structured symptoms if provided
            if (! empty($data['symptoms']) && is_array($data['symptoms'])) {
                $medicalRecord->symptoms()->delete(); // reset on recreate
                foreach ($data['symptoms'] as $symptomData) {
                    Symptom::create([
                        'medical_record_id' => $medicalRecord->id,
                        'name' => $symptomData['name'],
                        'description' => $symptomData['description'] ?? null,
                        'severity' => $symptomData['severity'] ?? 'moderate',
                        'duration' => $symptomData['duration'] ?? null,
                        'onset' => $symptomData['onset'] ?? null,
                        'frequency' => $symptomData['frequency'] ?? null,
                    ]);
                }
            }

            // Write Medical Access Audit Log
            MedicalAccessLog::create([
                'actor_id' => $doctorUser->id,
                'patient_id' => $consultation->patient_id,
                'medical_record_id' => $medicalRecord->id,
                'access_context' => 'CREATE_MEDICAL_RECORD',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);

            return $medicalRecord->load(['symptoms', 'versions']);
        });
    }

    /**
     * Amend an existing medical record creating a historical version snapshot.
     */
    public function amendMedicalRecord(User $author, MedicalRecord $record, array $data, string $changeReason): MedicalRecord
    {
        return DB::transaction(function () use ($author, $record, $data, $changeReason) {
            $nextVersionNumber = ($record->versions()->max('version_number') ?? 0) + 1;

            // 1. Create historical version snapshot
            MedicalRecordVersion::create([
                'medical_record_id' => $record->id,
                'author_id' => $author->id,
                'version_number' => $nextVersionNumber,
                'chief_complaint' => $record->chief_complaint,
                'examination_notes' => $record->examination_notes,
                'diagnosis_notes' => $record->diagnosis_notes,
                'treatment_plan' => $record->treatment_plan,
                'change_reason' => $changeReason,
                'created_at' => now(),
            ]);

            // 2. Update current active record
            $record->update([
                'chief_complaint' => $data['chief_complaint'] ?? $record->chief_complaint,
                'examination_notes' => $data['examination_notes'] ?? $record->examination_notes,
                'diagnosis_notes' => $data['diagnosis_notes'] ?? $record->diagnosis_notes,
                'treatment_plan' => $data['treatment_plan'] ?? $record->treatment_plan,
            ]);

            // Audit log
            MedicalAccessLog::create([
                'actor_id' => $author->id,
                'patient_id' => $record->patient_id,
                'medical_record_id' => $record->id,
                'access_context' => 'AMEND_MEDICAL_RECORD',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);

            return $record->fresh(['versions', 'symptoms']);
        });
    }

    /**
     * Get patient medical history with access auditing.
     */
    public function getPatientMedicalHistory(User $actor, int $patientId, ?string $accessReason = null): array
    {
        $actorDoctorId = $actor->doctorProfile?->id;
        $actorPatientId = $actor->patientProfile?->id;

        $isOwnPatientHistory = ($actorPatientId === $patientId);
        $hasDoctorRelation = $actorDoctorId
            ? Consultation::where('patient_id', $patientId)->where('doctor_id', $actorDoctorId)->exists()
            : false;

        $isAdmin = method_exists($actor, 'hasRole') && $actor->hasRole('Super Admin');

        if (! $isOwnPatientHistory && ! $hasDoctorRelation && ! $isAdmin) {
            throw ValidationException::withMessages([
                'authorization' => ['غير مصرح لك بالاطلاع على السجل الطبي لهذا المريض.'],
            ]);
        }

        // Write Audit Log if Doctor or Admin accesses
        if (! $isOwnPatientHistory) {
            MedicalAccessLog::create([
                'actor_id' => $actor->id,
                'patient_id' => $patientId,
                'access_context' => 'VIEW_PATIENT_MEDICAL_HISTORY',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);
        }

        return MedicalRecord::with(['doctor.user', 'consultation', 'symptoms', 'versions.author'])
            ->where('patient_id', $patientId)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }
}
