<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'patient' => [
                'id' => $this->patient?->id,
                'name' => $this->patient?->first_name.' '.$this->patient?->last_name,
                'phone' => $this->patient?->user?->phone,
            ],
            'doctor' => [
                'id' => $this->doctor?->id,
                'name' => $this->doctor?->user?->full_name,
                'specialty' => $this->doctor?->primarySpecialty?->name_ar,
            ],
            'appointment_type' => [
                'id' => $this->appointmentType?->id,
                'name_ar' => $this->appointmentType?->name_ar,
                'code' => $this->appointmentType?->code,
            ],
            'appointment_date' => $this->appointment_date?->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'price_snapshot_yer' => (float) $this->price_snapshot,
            'status' => $this->status?->value ?? $this->status,
            'locked_until' => $this->locked_until?->toIso8601String(),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
