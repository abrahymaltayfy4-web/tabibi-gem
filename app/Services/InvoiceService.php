<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Generate invoice for succeeded payment.
     */
    public function generateInvoice(Payment $payment): Invoice
    {
        return DB::transaction(function () use ($payment) {
            $existing = Invoice::where('payment_id', $payment->id)->first();
            if ($existing) {
                return $existing;
            }

            $datePrefix = now()->format('Ym');
            $count = Invoice::where('invoice_number', 'like', "INV-YER-{$datePrefix}-%")->count() + 1;
            $sequence = str_pad((string) $count, 5, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-YER-{$datePrefix}-{$sequence}";

            return Invoice::create([
                'invoice_number' => $invoiceNumber,
                'payment_id' => $payment->id,
                'patient_id' => $payment->patient_id,
                'doctor_id' => $payment->appointment->doctor_id,
                'total_amount' => $payment->amount,
            ]);
        });
    }
}
