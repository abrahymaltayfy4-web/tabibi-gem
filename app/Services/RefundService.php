<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorEarning;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    /**
     * Process full or partial refund for a payment based on cancellation rules.
     */
    public function processRefund(User $actor, int $paymentId, string $reason): Refund
    {
        return DB::transaction(function () use ($actor, $paymentId, $reason) {
            $payment = Payment::with(['appointment', 'patient'])->lockForUpdate()->findOrFail($paymentId);

            if ($payment->payment_status === PaymentStatus::REFUNDED->value) {
                throw ValidationException::withMessages([
                    'refund' => ['تم استرداد هذا المبلغ بالكامل في وقت سابق.'],
                ]);
            }

            $appointment = $payment->appointment;
            $now = now();
            $dateStr = $appointment->appointment_date instanceof \DateTimeInterface
                ? $appointment->appointment_date->format('Y-m-d')
                : substr((string) $appointment->appointment_date, 0, 10);
            $appointmentStart = Carbon::parse("{$dateStr} {$appointment->start_time}");

            // Rule: Cancellation > 24 hours = 100% refund, < 24 hours = 80% partial refund
            $hoursUntilAppointment = $now->diffInHours($appointmentStart, false);
            $refundPercentage = ($hoursUntilAppointment >= 24) ? 1.0 : 0.8;

            $refundAmount = round((float) $payment->amount * $refundPercentage, 2);

            $refund = Refund::create([
                'payment_id' => $payment->id,
                'amount' => $refundAmount,
                'reason' => $reason,
                'status' => 'Completed',
                'approved_by_admin_id' => $actor->id,
                'processed_at' => now(),
            ]);

            $payment->payment_status = ($refundPercentage === 1.0)
                ? PaymentStatus::REFUNDED->value
                : PaymentStatus::PARTIALLY_REFUNDED->value;
            $payment->save();

            // Cancel appointment & update earnings
            $appointment->status = AppointmentStatus::CANCELLED_BY_PATIENT->value;
            $appointment->cancellation_reason = $reason;
            $appointment->save();

            DoctorEarning::where('appointment_id', $appointment->id)->delete();

            return $refund;
        });
    }
}
