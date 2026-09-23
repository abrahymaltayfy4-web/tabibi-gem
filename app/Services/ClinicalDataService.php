<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Diagnosis;
use App\Models\MedicalAccessLog;
use App\Models\MedicalDocument;
use App\Models\MedicalRecord;
use App\Models\PatientMedicalProfile;
use App\Models\PatientProfile;
use App\Models\TreatmentPlan;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ClinicalDataService
{
    /**
     * Get or create patient medical profile.
     */
    public function getPatientProfile(User $actor, int $patientId): PatientMedicalProfile
    {
        $patient = PatientProfile::findOrFail($patientId);

        // Audit log
        MedicalAccessLog::create([
            'actor_id' => $actor->id,
            'patient_id' => $patient->id,
            'access_context' => 'VIEW_MEDICAL_PROFILE',
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);

        return PatientMedicalProfile::firstOrCreate(
            ['patient_id' => $patient->id],
            [
                'uuid' => (string) Str::uuid(),
                'blood_type' => 'O+',
                'height_cm' => 170,
                'weight_kg' => 70,
            ]
        );
    }

    /**
     * Create comprehensive SOAP clinical record with diagnoses and treatment plan inside DB transaction.
     */
    public function createComprehensiveClinicalRecord(User $doctorUser, int $consultationId, array $data): MedicalRecord
    {
        return DB::transaction(function () use ($doctorUser, $consultationId, $data) {
            $consultation = Consultation::findOrFail($consultationId);
            $doctorId = $doctorUser->doctorProfile?->id;

            if ($consultation->doctor_id !== $doctorId) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بتدوين سجل طبي لهذه الاستشارة.'],
                ]);
            }

            // 1. Create or Update Medical Record
            $record = MedicalRecord::updateOrCreate(
                ['consultation_id' => $consultationId],
                [
                    'uuid' => (string) Str::uuid(),
                    'patient_id' => $consultation->patient_id,
                    'doctor_id' => $doctorId,
                    'chief_complaint' => $data['chief_complaint'] ?? '',
                    'examination_notes' => $data['examination_notes'] ?? null,
                    'diagnosis_notes' => $data['diagnosis_notes'] ?? '',
                    'treatment_plan' => $data['treatment_plan'] ?? null,
                    'is_finalized' => true,
                ]
            );

            // 2. Add Structured Diagnoses
            if (! empty($data['diagnoses']) && is_array($data['diagnoses'])) {
                foreach ($data['diagnoses'] as $diagData) {
                    Diagnosis::create([
                        'uuid' => (string) Str::uuid(),
                        'medical_record_id' => $record->id,
                        'patient_id' => $consultation->patient_id,
                        'doctor_id' => $doctorId,
                        'diagnosis_name' => $diagData['diagnosis_name'],
                        'icd_code' => $diagData['icd_code'] ?? null,
                        'diagnosis_type' => $diagData['diagnosis_type'] ?? 'primary',
                        'clinical_notes' => $diagData['clinical_notes'] ?? null,
                    ]);
                }
            }

            // 3. Add Treatment Plan
            if (! empty($data['treatment_plan_details'])) {
                TreatmentPlan::updateOrCreate(
                    ['medical_record_id' => $record->id],
                    [
                        'uuid' => (string) Str::uuid(),
                        'recommendations' => $data['treatment_plan_details']['recommendations'],
                        'lifestyle_guidance' => $data['treatment_plan_details']['lifestyle_guidance'] ?? null,
                        'follow_up_date' => $data['treatment_plan_details']['follow_up_date'] ?? null,
                    ]
                );
            }

            // 4. Audit Log
            MedicalAccessLog::create([
                'actor_id' => $doctorUser->id,
                'patient_id' => $consultation->patient_id,
                'medical_record_id' => $record->id,
                'access_context' => 'CREATE_COMPREHENSIVE_CLINICAL_RECORD',
                'ip_address' => request()->ip(),
                'accessed_at' => now(),
            ]);

            return $record->load(['symptoms', 'versions', 'diagnoses', 'treatmentPlan']);
        });
    }

    /**
     * Upload medical document securely to private storage.
     */
    public function uploadMedicalDocument(User $uploader, int $patientId, UploadedFile $file, string $documentType, ?int $medicalRecordId = null): MedicalDocument
    {
        $patient = PatientProfile::findOrFail($patientId);

        // Security check on MIME
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp', 'application/dicom'];
        if (! in_array($file->getMimeType(), $allowedMimes)) {
            throw ValidationException::withMessages([
                'file' => ['نوع الملف غير مسموح به. يرجى رفع ملف بصيغة PDF أو صورة طاقم طبي.'],
            ]);
        }

        $storedPath = $file->store('private/medical_documents', 'local');
        $sha256 = hash_file('sha256', $file->getRealPath());

        $doc = MedicalDocument::create([
            'uuid' => (string) Str::uuid(),
            'patient_id' => $patient->id,
            'uploaded_by_user_id' => $uploader->id,
            'medical_record_id' => $medicalRecordId,
            'document_type' => $documentType,
            'original_filename' => $file->getClientOriginalName(),
            'storage_disk' => 'local',
            'storage_path' => $storedPath,
            'mime_type' => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
            'checksum_sha256' => $sha256,
        ]);

        // Audit Log
        MedicalAccessLog::create([
            'actor_id' => $uploader->id,
            'patient_id' => $patient->id,
            'access_context' => 'UPLOAD_MEDICAL_DOCUMENT',
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);

        return $doc;
    }
}
