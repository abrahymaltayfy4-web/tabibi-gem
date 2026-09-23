<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\PaymentTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentTransactionService $paymentService
    ) {}

    public function checkout(int $appointmentId, Request $request): JsonResponse
    {
        $result = $this->paymentService->initiateCheckout($request->user(), $appointmentId);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تفعيل عملية الدفع بالريال اليمني (Sandboxed YER)',
            'data' => $result,
        ]);
    }

    public function callback(string $txRef): JsonResponse
    {
        $payment = $this->paymentService->completePayment($txRef);

        return response()->json([
            'status' => 'success',
            'message' => 'تم التحقق من نجاح الدفع بالريال اليمني وتأكيد الموعد Confirmed',
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->payment_status,
                'appointment_status' => $payment->appointment?->status,
                'amount_yer' => (float) $payment->amount,
                'transaction_reference' => $payment->transaction_reference,
            ],
        ]);
    }

    public function uploadManualProof(int $appointmentId, Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'reference_number' => 'required|string|max:100',
        ]);

        $payment = $this->paymentService->uploadManualProof(
            $request->user(),
            $appointmentId,
            $request->file('file'),
            $request->input('reference_number')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع إشعار التحويل اليدوي بنجاح وقيد المراجعة لدى المدقق المالي.',
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->payment_status,
                'transaction_reference' => $payment->transaction_reference,
            ],
        ], 201);
    }

    public function invoice(int $invoiceId): JsonResponse
    {
        $invoice = Invoice::with(['patient.user', 'doctor.user', 'payment'])->findOrFail($invoiceId);

        return response()->json([
            'status' => 'success',
            'data' => [
                'invoice_number' => $invoice->invoice_number,
                'total_amount_yer' => (float) $invoice->total_amount,
                'patient_name' => $invoice->patient->user?->full_name,
                'doctor_name' => $invoice->doctor->user?->full_name,
                'issued_at' => $invoice->created_at->toIso8601String(),
            ],
        ]);
    }
}
