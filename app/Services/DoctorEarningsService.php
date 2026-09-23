<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorEarning;
use Illuminate\Support\Facades\DB;

class DoctorEarningsService
{
    /**
     * Record 15% platform fee & net doctor earning.
     */
    public function recordPendingEarning(Appointment $appointment, float $grossAmount): DoctorEarning
    {
        return DB::transaction(function () use ($appointment, $grossAmount) {
            $existing = DoctorEarning::where('appointment_id', $appointment->id)->first();
            if ($existing) {
                return $existing;
            }

            $platformFee = round($grossAmount * 0.15, 2);
            $netDoctorAmount = round($grossAmount - $platformFee, 2);

            return DoctorEarning::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'gross_amount' => $grossAmount,
                'platform_fee_amount' => $platformFee,
                'net_doctor_amount' => $netDoctorAmount,
                'status' => 'Pending',
            ]);
        });
    }

    /**
     * Transition earning status from Pending to Available upon consultation completion.
     */
    public function markEarningAvailable(int $appointmentId): void
    {
        DoctorEarning::where('appointment_id', $appointmentId)
            ->where('status', 'Pending')
            ->update(['status' => 'Available']);
    }
}
