<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'primary_specialty' => [
                'id' => $this->primarySpecialty?->id,
                'name_ar' => $this->primarySpecialty?->name_ar,
                'name_en' => $this->primarySpecialty?->name_en,
                'code' => $this->primarySpecialty?->code,
            ],
            'license_number' => $this->license_number,
            'verification_status' => $this->verification_status?->value ?? $this->verification_status,
            'consultation_price_yer' => (float) $this->consultation_price,
            'consultation_duration_minutes' => $this->consultation_duration_minutes,
            'is_active_clinic' => (bool) $this->is_active_clinic,
            'bio_ar' => $this->bio_ar,
            'years_of_experience' => $this->years_of_experience,
            'rating_avg' => (float) $this->rating_avg,
            'rating_count' => (int) $this->rating_count,
        ];
    }
}
