<?php

namespace App\Services;

use App\Domain\Payment\Contracts\PaymentGatewayInterface;
use App\Models\Appointment;
use App\Models\File;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\PaymentStatus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentTransactionService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected InvoiceService $invoiceService,
        protected DoctorEarningsService $earningsService
    ) {}

    /**
     * Initiate appointment checkout.
     */
    public function initiateCheckout(User $patient, int $appointmentId): array
    {
        return DB::transaction(function () use ($patient, $appointmentId) {
            $appointment = Appointment::lockForUpdate()->findOrFail($appointmentId);

            if ($appointment->patient_id !== $patient->patientProfile?->id) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بإجراء الدفع لهذا الموعد.'],
                ]);
            }

            $currentStatus = $appointment->status instanceof AppointmentStatus
                ? $appointment->status->value
                : $appointment->status;

            if (in_array($currentStatus, ['Confirmed', 'Paid', 'Completed', 'CancelledByPatient', 'CancelledByDoctor'])) {
                throw ValidationException::withMessages([
                    'appointment_status' => ['هذا الموعد مؤكد بالفعل أو ملغى ولا يتطلب دفعاً جديداً.'],
                ]);
            }

            $txRef = 'TX-YER-'.strtoupper(Str::random(10));

            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'amount' => $appointment->price_snapshot,
                'currency' => 'YER',
                'payment_status' => PaymentStatus::PENDING->value,
                'payment_gateway' => 'Sandboxed_YER',
                'transaction_reference' => $txRef,
            ]);

            $gatewayResult = $this->gateway->initiatePayment([
                'transaction_reference' => $txRef,
                'amount' => (float) $appointment->price_snapshot,
            ]);

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway_name' => 'Sandboxed_YER',
                'request_payload_json' => ['amount' => $appointment->price_snapshot],
                'response_payload_json' => $gatewayResult,
                'gateway_transaction_id' => null,
                'status' => 'Pending',
            ]);

            return [
                'payment_id' => $payment->id,
                'transaction_reference' => $txRef,
                'amount' => $payment->amount,
                'currency' => 'YER',
                'checkout_url' => $gatewayResult['checkout_url'],
            ];
        });
    }

    /**
     * Complete payment & confirm appointment atomically.
     */
    public function completePayment(string $transactionReference): Payment
    {
        return DB::transaction(function () use ($transactionReference) {
            $payment = Payment::with(['appointment', 'patient'])
                ->lockForUpdate()
                ->where('transaction_reference', $transactionReference)
                ->firstOrFail();

            if ($payment->payment_status === PaymentStatus::SUCCEEDED->value || $payment->payment_status === PaymentStatus::PAID->value) {
                return $payment;
            }

            $verification = $this->gateway->verifyPayment($transactionReference);

            $payment->payment_status = PaymentStatus::SUCCEEDED->value;
            $payment->paid_at = now();
            $payment->save();

            // Confirm appointment
            $appointment = $payment->appointment;
            $appointment->status = AppointmentStatus::CONFIRMED->value;
            $appointment->save();

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway_name' => 'Sandboxed_YER',
                'response_payload_json' => $verification,
                'gateway_transaction_id' => $verification['gateway_transaction_id'] ?? null,
                'status' => 'Succeeded',
            ]);

            // Generate Invoice & Record 15% Platform Fee Earning
            $this->invoiceService->generateInvoice($payment);
            $this->earningsService->recordPendingEarning($appointment, (float) $payment->amount);

            return $payment;
        });
    }

    /**
     * Upload manual payment receipt for verification.
     */
    public function uploadManualProof(User $patient, int $appointmentId, UploadedFile $file, string $referenceNumber): Payment
    {
        return DB::transaction(function () use ($patient, $appointmentId, $file, $referenceNumber) {
            $appointment = Appointment::lockForUpdate()->findOrFail($appointmentId);

            $uuid = (string) Str::uuid();
            $storedName = "{$uuid}.".$file->getClientOriginalExtension();
            $path = $file->storeAs("private/payment_proofs/{$appointmentId}", $storedName, 'local');

            $fileModel = File::create([
                'uuid' => $uuid,
                'uploader_id' => $patient->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'storage_disk' => 'local',
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_bytes' => $file->getSize(),
                'category' => 'payment_proof',
            ]);

            $txRef = 'MAN-YER-'.strtoupper(Str::random(8));

            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'amount' => $appointment->price_snapshot,
                'currency' => 'YER',
                'payment_status' => PaymentStatus::PROCESSING->value,
                'payment_gateway' => 'Manual_Bank_Transfer',
                'transaction_reference' => $txRef,
            ]);

            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'gateway_name' => 'Manual_Bank_Transfer',
                'request_payload_json' => [
                    'reference_number' => $referenceNumber,
                    'file_id' => $fileModel->id,
                ],
                'status' => 'UnderReview',
            ]);

            return $payment;
        });
    }
}
