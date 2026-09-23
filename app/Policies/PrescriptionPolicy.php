<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    /**
     * Determine whether the user can view the prescription.
     */
    public function view(User $user, Prescription $prescription): bool
    {
        if ($user->patientProfile && $user->patientProfile->id === $prescription->patient_id) {
            return true;
        }

        if ($user->doctorProfile && $user->doctorProfile->id === $prescription->doctor_id) {
            return true;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update/amend the prescription.
     */
    public function update(User $user, Prescription $prescription): bool
    {
        return $user->doctorProfile && $user->doctorProfile->id === $prescription->doctor_id;
    }
}
