<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Infrastructure\Adapters\Payment\SandboxedYERPaymentAdapter;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponse;

    protected SandboxedYERPaymentAdapter $paymentGateway;

    public function __construct(SandboxedYERPaymentAdapter $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function checkout(int $appointmentId, Request $request): JsonResponse
    {
        $appointment = Appointment::findOrFail($appointmentId);

        $result = $this->paymentGateway->initiatePayment($appointment);

        return $this->successResponse(
            data: $result,
            message: 'تم تفعيل عملية الدفع عبر محاكي الريال اليمني (Sandboxed YER)'
        );
    }

    public function callback(string $txRef, Request $request): JsonResponse
    {
        $payment = $this->paymentGateway->verifyPayment($txRef, $request->all());

        return $this->successResponse(
            data: [
                'payment_id' => $payment->id,
                'status' => $payment->payment_status?->value ?? $payment->payment_status,
                'appointment_status' => $payment->appointment?->status?->value ?? $payment->appointment?->status,
                'amount_yer' => (float) $payment->amount,
            ],
            message: 'تم التحقق من نجاح الدفع بالريال اليمني وتأكيد الموعد Confirmed'
        );
    }
}
