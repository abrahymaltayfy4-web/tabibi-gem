<?php

namespace App\Domains\Booking\Actions;

use App\Models\Appointment;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Shared\Enums\AppointmentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateAppointmentAction
{
    public function execute(PatientProfile $patient, array $data): Appointment
    {
        return DB::transaction(function () use ($patient, $data) {
            $doctorId = $data['doctor_id'];
            $appointmentDate = $data['appointment_date'];
            $startTime = $data['start_time'];

            // 1. Fetch doctor profile and verify active clinic status
            $doctor = DoctorProfile::findOrFail($doctorId);
            if (! $doctor->is_active_clinic) {
                throw ValidationException::withMessages([
                    'doctor_id' => ['عيادة هذا الطبيب غير متاحة للحجز حالياً.'],
                ]);
            }

            // 2. Calculate end time based on doctor's consultation duration
            $durationMinutes = $doctor->consultation_duration_minutes ?? 30;
            $endTime = Carbon::createFromFormat('H:i', $startTime)
                ->addMinutes($durationMinutes)
                ->format('H:i:s');

            // 3. Atomic Lock & Overlap Check using SELECT FOR UPDATE
            $overlappingAppointment = Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $appointmentDate)
                ->where('start_time', $startTime)
                ->whereIn('status', [
                    AppointmentStatus::PENDING->value,
                    AppointmentStatus::AWAITING_PAYMENT->value,
                    AppointmentStatus::PAID->value,
                    AppointmentStatus::CONFIRMED->value,
                    AppointmentStatus::UPCOMING->value,
                    AppointmentStatus::IN_PROGRESS->value,
                ])
                ->lockForUpdate()
                ->first();

            if ($overlappingAppointment) {
                throw ValidationException::withMessages([
                    'start_time' => ['هذه الفترة الزمنية محجوزة بالفعل أو قيد المعالجة مع مريض آخر.'],
                ]);
            }

            // 4. Create appointment record with price snapshot and 10-min locking window
            $appointment = Appointment::create([
                'uuid' => (string) Str::uuid(),
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'appointment_type_id' => $data['appointment_type_id'],
                'appointment_date' => $appointmentDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price_snapshot' => $doctor->consultation_price,
                'status' => AppointmentStatus::AWAITING_PAYMENT,
                'locked_until' => now()->addMinutes(10),
                'notes' => $data['notes'] ?? null,
            ]);

            return $appointment->load(['patient.user', 'doctor.user', 'doctor.primarySpecialty', 'appointmentType']);
        });
    }
}
