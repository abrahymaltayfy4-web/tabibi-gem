<?php

namespace App\Services;

use App\Models\MedicalAccessLog;
use App\Models\PatientMedicalHistory;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class PatientMedicalHistoryService
{
    /**
     * Get patient's longitudinal medical history with audit logging.
     */
    public function getPatientHistory(PatientProfile $patient, User $actor, string $accessContext = 'VIEW_MEDICAL_HISTORY'): Collection
    {
        // Audit log
        MedicalAccessLog::create([
            'actor_id' => $actor->id,
            'patient_id' => $patient->id,
            'access_context' => $accessContext,
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);

        return PatientMedicalHistory::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Add a medical history entry for a patient.
     */
    public function addHistoryEntry(PatientProfile $patient, User $actor, array $data): PatientMedicalHistory
    {
        $history = PatientMedicalHistory::create([
            'uuid' => (string) Str::uuid(),
            'patient_id' => $patient->id,
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'diagnosed_date' => $data['diagnosed_date'] ?? null,
            'status' => $data['status'] ?? 'active',
            'created_by_user_id' => $actor->id,
        ]);

        // Audit log
        MedicalAccessLog::create([
            'actor_id' => $actor->id,
            'patient_id' => $patient->id,
            'access_context' => 'CREATE_MEDICAL_HISTORY_ENTRY',
            'ip_address' => request()->ip(),
            'accessed_at' => now(),
        ]);

        return $history;
    }
}
