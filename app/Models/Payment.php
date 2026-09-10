<?php

namespace App\Models;

use App\Shared\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'appointment_id',
        'patient_id',
        'amount',
        'currency',
        'payment_status',
        'payment_gateway',
        'transaction_reference',
        'paid_at',
    ];

    protected $casts = [
        'payment_status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class);
    }
}
