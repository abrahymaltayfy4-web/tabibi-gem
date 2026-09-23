<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\User;

class PatientProfilePolicy
{
    /**
     * Determine whether the user can view the patient profile.
     */
    public function view(User $user, PatientProfile $patientProfile): bool
    {
        // 1. Patient viewing their own profile
        if ($user->id === $patientProfile->user_id) {
            return true;
        }

        // 2. Admin viewing profile
        if ($user->isAdmin()) {
            return true;
        }

        // 3. Doctor viewing profile if there is an existing clinical relationship
        if ($user->doctorProfile) {
            $doctorId = $user->doctorProfile->id;

            return Appointment::where('doctor_id', $doctorId)
                ->where('patient_id', $patientProfile->id)
                ->exists();
        }

        return false;
    }
}
