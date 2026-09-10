<?php

namespace App\Infrastructure\Adapters\Payment;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\DoctorEarning;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SandboxedYERPaymentAdapter implements PaymentGatewayInterface
{
    public function initiatePayment(Appointment $appointment): array
    {
        $txRef = 'YER-' . strtoupper(Str::random(10));

        $payment = Payment::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'amount' => $appointment->price_snapshot,
            'currency' => 'YER',
            'payment_status' => PaymentStatus::PENDING,
            'payment_gateway' => 'Sandbox_YER',
            'transaction_reference' => $txRef,
        ]);

        return [
            'payment_id' => $payment->id,
            'transaction_reference' => $txRef,
            'amount_yer' => (float) $payment->amount,
            'checkout_url' => config('app.url') . '/api/v1/payments/sandbox-checkout/' . $txRef,
        ];
    }

    public function verifyPayment(string $transactionReference, array $payload): Payment
    {
        return DB::transaction(function () use ($transactionReference, $payload) {
            $payment = Payment::where('transaction_reference', $transactionReference)
                ->firstOrFail();

            $payment->update([
                'payment_status' => PaymentStatus::PAID,
                'paid_at' => now(),
            ]);

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway_name' => 'Sandbox_YER',
                'request_payload_json' => $payload,
                'response_payload_json' => ['status' => 'SUCCESS', 'gateway_ref' => 'SANDBOX-' . time()],
                'gateway_transaction_id' => 'SANDBOX-' . time(),
                'status' => 'PAID',
            ]);

            // Confirm appointment and release locking window
            $appointment = $payment->appointment;
            $appointment->update([
                'status' => AppointmentStatus::CONFIRMED,
                'locked_until' => null,
            ]);

            // Calculate doctor earnings (15% platform fee)
            $gross = $payment->amount;
            $platformFee = $gross * 0.15;
            $netDoctor = $gross - $platformFee;

            DoctorEarning::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'gross_amount' => $gross,
                'platform_fee_amount' => $platformFee,
                'net_doctor_amount' => $netDoctor,
                'status' => 'Pending',
            ]);

            // Generate Invoice
            Invoice::create([
                'invoice_number' => 'INV-YER-' . time(),
                'payment_id' => $payment->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'total_amount' => $gross,
            ]);

            return $payment->fresh(['appointment', 'patient.user']);
        });
    }
}
