<?php

namespace App\Services;

use App\Models\DoctorEarning;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayoutService
{
    /**
     * Doctor requests payout of available earnings balance.
     */
    public function requestPayout(User $doctorUser, float $amount): Payout
    {
        return DB::transaction(function () use ($doctorUser, $amount) {
            $doctorId = $doctorUser->doctorProfile?->id;

            if (! $doctorId) {
                throw ValidationException::withMessages([
                    'authorization' => ['الحساب الحالي ليس حساب طبيب معتمد.'],
                ]);
            }

            $availableBalance = (float) DoctorEarning::where('doctor_id', $doctorId)
                ->where('status', 'Available')
                ->sum('net_doctor_amount');

            if ($amount > $availableBalance || $amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => ["المبلغ المطلوب ({$amount} YER) يتجاوز الرصيد المتاح للسحب ({$availableBalance} YER)."],
                ]);
            }

            return Payout::create([
                'doctor_id' => $doctorId,
                'amount' => $amount,
                'currency' => 'YER',
                'status' => 'Requested',
                'requested_at' => now(),
            ]);
        });
    }

    /**
     * Admin approves payout and marks doctor earnings as PaidOut.
     */
    public function approvePayout(User $adminUser, int $payoutId, string $transferReference, ?string $notes = null): Payout
    {
        return DB::transaction(function () use ($adminUser, $payoutId, $transferReference, $notes) {
            $payout = Payout::lockForUpdate()->findOrFail($payoutId);

            if ($payout->status === 'Completed' || $payout->status === 'Approved') {
                return $payout;
            }

            $payout->status = 'Completed';
            $payout->transfer_reference = $transferReference;
            $payout->approved_by_admin_id = $adminUser->id;
            $payout->processed_at = now();
            $payout->notes = $notes;
            $payout->save();

            // Mark earnings as PaidOut
            DoctorEarning::where('doctor_id', $payout->doctor_id)
                ->where('status', 'Available')
                ->update(['status' => 'PaidOut', 'payout_date' => now()]);

            return $payout;
        });
    }
}
