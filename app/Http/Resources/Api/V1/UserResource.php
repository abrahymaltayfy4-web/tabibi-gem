<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'account_status' => $this->account_status?->value ?? $this->account_status,
            'roles' => $this->roles->pluck('name'),
            'patient_profile' => new PatientProfileResource($this->whenLoaded('patientProfile')),
            'doctor_profile' => new DoctorProfileResource($this->whenLoaded('doctorProfile')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
