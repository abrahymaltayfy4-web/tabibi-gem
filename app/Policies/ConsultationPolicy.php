<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    /**
     * Determine whether the user can join or interact with the consultation.
     */
    public function join(User $user, Consultation $consultation): bool
    {
        if ($user->patientProfile && $user->patientProfile->id === $consultation->patient_id) {
            return true;
        }

        if ($user->doctorProfile && $user->doctorProfile->id === $consultation->doctor_id) {
            return true;
        }

        return false;
    }
}
