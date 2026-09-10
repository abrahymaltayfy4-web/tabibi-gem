<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'gross_amount',
        'platform_fee_amount',
        'net_doctor_amount',
        'status',
        'payout_date',
    ];

    protected $casts = [
        'payout_date' => 'datetime',
        'gross_amount' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'net_doctor_amount' => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }
}
