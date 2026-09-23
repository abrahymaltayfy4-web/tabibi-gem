<?php

namespace App\Domain\Payment\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Initiate payment request for appointment.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function initiatePayment(array $payload): array;

    /**
     * Verify payment transaction reference.
     *
     * @return array<string, mixed>
     */
    public function verifyPayment(string $transactionReference): array;

    /**
     * Process refund for a transaction.
     *
     * @return array<string, mixed>
     */
    public function processRefund(string $transactionReference, float $amount, string $reason): array;

    /**
     * Handle incoming gateway webhook event.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     * @return array<string, mixed>
     */
    public function handleWebhook(array $payload, array $headers): array;
}
