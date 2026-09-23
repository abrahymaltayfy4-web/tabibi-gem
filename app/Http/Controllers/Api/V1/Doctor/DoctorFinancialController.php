<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Http\Controllers\Controller;
use App\Models\DoctorEarning;
use App\Models\Payout;
use App\Services\PayoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorFinancialController extends Controller
{
    public function __construct(
        protected PayoutService $payoutService
    ) {}

    public function earnings(Request $request): JsonResponse
    {
        $doctorId = $request->user()->doctorProfile?->id;

        if (! $doctorId) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح للوصول.',
            ], 403);
        }

        $grossRevenue = (float) DoctorEarning::where('doctor_id', $doctorId)->sum('gross_amount');
        $platformFeeTotal = (float) DoctorEarning::where('doctor_id', $doctorId)->sum('platform_fee_amount');
        $netEarningsTotal = (float) DoctorEarning::where('doctor_id', $doctorId)->sum('net_doctor_amount');

        $availableBalance = (float) DoctorEarning::where('doctor_id', $doctorId)
            ->where('status', 'Available')
            ->sum('net_doctor_amount');

        $pendingBalance = (float) DoctorEarning::where('doctor_id', $doctorId)
            ->where('status', 'Pending')
            ->sum('net_doctor_amount');

        $paidOutTotal = (float) Payout::where('doctor_id', $doctorId)
            ->where('status', 'Completed')
            ->sum('amount');

        $payoutHistory = Payout::where('doctor_id', $doctorId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'gross_revenue_yer' => $grossRevenue,
                'platform_fee_total_yer' => $platformFeeTotal,
                'net_earnings_total_yer' => $netEarningsTotal,
                'available_balance_yer' => $availableBalance,
                'pending_balance_yer' => $pendingBalance,
                'paid_out_total_yer' => $paidOutTotal,
                'payout_history' => $payoutHistory,
            ],
        ]);
    }

    public function requestPayout(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $payout = $this->payoutService->requestPayout(
            $request->user(),
            (float) $request->input('amount')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع طلب سحب الأرباح بنجاح وقيد المراجعة الإدارية.',
            'data' => $payout,
        ], 201);
    }
}
