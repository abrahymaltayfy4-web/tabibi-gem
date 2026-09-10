<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payment_id',
        'gateway_name',
        'request_payload_json',
        'response_payload_json',
        'gateway_transaction_id',
        'status',
    ];

    protected $casts = [
        'request_payload_json' => 'array',
        'response_payload_json' => 'array',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
