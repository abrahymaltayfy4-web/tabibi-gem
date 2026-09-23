<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorEarning;
use App\Models\Payment;
use App\Models\Payout;
use App\Services\PaymentTransactionService;
use App\Services\PayoutService;
use App\Services\RefundService;
use App\Shared\Enums\PaymentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminFinancialController extends Controller
{
    public function __construct(
        protected PaymentTransactionService $paymentService,
        protected RefundService $refundService,
        protected PayoutService $payoutService
    ) {}

    public function payments(Request $request): JsonResponse
    {
        $payments = Payment::with(['patient.user', 'appointment.doctor.user'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        $grossPlatformRevenue = (float) Payment::whereIn('payment_status', ['Succeeded', 'Paid'])->sum('amount');
        $platformFeeRevenue = (float) DoctorEarning::sum('platform_fee_amount');

        return response()->json([
            'status' => 'success',
            'data' => $payments->items(),
            'summary' => [
                'gross_platform_revenue_yer' => $grossPlatformRevenue,
                'platform_fee_revenue_15_percent_yer' => $platformFeeRevenue,
            ],
            'meta' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
            ],
        ]);
    }

    public function verifyManual(Request $request, int $paymentId): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:Approved,Rejected',
            'reason' => 'nullable|string|max:255',
        ]);

        $payment = Payment::findOrFail($paymentId);

        if ($request->input('status') === 'Approved') {
            $payment = $this->paymentService->completePayment($payment->transaction_reference);
        } else {
            $payment->payment_status = PaymentStatus::FAILED->value;
            $payment->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم معالجة إشعار الدفع اليدوي بنجاح.',
            'data' => $payment,
        ]);
    }

    public function refund(Request $request, int $paymentId): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $refund = $this->refundService->processRefund(
            $request->user(),
            $paymentId,
            $request->input('reason')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم معالجة استرداد المبلغ وإلغاء الموعد.',
            'data' => $refund,
        ]);
    }

    public function payouts(Request $request): JsonResponse
    {
        $payouts = Payout::with(['doctor.user', 'approvedBy'])
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $payouts->items(),
        ]);
    }

    public function approvePayout(Request $request, int $payoutId): JsonResponse
    {
        $request->validate([
            'transfer_reference' => 'required|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        $payout = $this->payoutService->approvePayout(
            $request->user(),
            $payoutId,
            $request->input('transfer_reference'),
            $request->input('notes')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم اعتماد تحويل الأرباح رسمياً وتأكيد العملية.',
            'data' => $payout,
        ]);
    }
}
