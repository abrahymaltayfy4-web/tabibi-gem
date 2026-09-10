<?php

namespace App\Domains\Payment\Contracts;

use App\Models\Appointment;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function initiatePayment(Appointment $appointment): array;

    public function verifyPayment(string $transactionReference, array $payload): Payment;
}
