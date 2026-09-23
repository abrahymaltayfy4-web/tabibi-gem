<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    /**
     * Determine whether the user can view the medical record.
     */
    public function view(User $user, MedicalRecord $record): bool
    {
        if ($user->patientProfile && $user->patientProfile->id === $record->patient_id) {
            return true;
        }

        if ($user->doctorProfile && $user->doctorProfile->id === $record->doctor_id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the medical record.
     */
    public function update(User $user, MedicalRecord $record): bool
    {
        return $user->doctorProfile && $user->doctorProfile->id === $record->doctor_id;
    }
}
