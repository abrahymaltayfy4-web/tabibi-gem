<?php

namespace App\Domain\Payment\Adapters;

use App\Domain\Payment\Contracts\PaymentGatewayInterface;

class SandboxedYERPaymentAdapter implements PaymentGatewayInterface
{
    public function initiatePayment(array $payload): array
    {
        $txRef = $payload['transaction_reference'] ?? 'TX-YER-'.strtoupper(bin2hex(random_bytes(6)));

        return [
            'status' => 'initiated',
            'gateway' => 'Sandboxed_YER_Gateway',
            'transaction_reference' => $txRef,
            'checkout_url' => url("/api/v1/payments/sandbox-callback/{$txRef}"),
            'currency' => 'YER',
            'amount' => $payload['amount'] ?? 0,
        ];
    }

    public function verifyPayment(string $transactionReference): array
    {
        return [
            'status' => 'Succeeded',
            'gateway_transaction_id' => 'GW-YER-'.rand(100000, 999999),
            'transaction_reference' => $transactionReference,
            'verified_at' => now()->toIso8601String(),
        ];
    }

    public function processRefund(string $transactionReference, float $amount, string $reason): array
    {
        return [
            'status' => 'Refunded',
            'refund_reference' => 'RF-YER-'.rand(100000, 999999),
            'amount' => $amount,
            'reason' => $reason,
            'processed_at' => now()->toIso8601String(),
        ];
    }

    public function handleWebhook(array $payload, array $headers): array
    {
        return [
            'event' => 'payment.succeeded',
            'transaction_reference' => $payload['transaction_reference'] ?? null,
            'verified' => true,
        ];
    }
}
