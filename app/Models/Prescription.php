<?php

namespace App\Models;

use App\Enums\PrescriptionState;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'prescription_number',
        'consultation_id',
        'doctor_id',
        'patient_id',
        'status',
        'parent_prescription_id',
        'amendment_reason',
        'finalized_by_user_id',
        'finalized_at',
        'notes',
        'issued_at',
        'expires_at',
    ];

    protected $casts = [
        'status' => PrescriptionState::class,
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function parentPrescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'parent_prescription_id');
    }

    public function childPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'parent_prescription_id');
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(PrescriptionAmendment::class, 'original_prescription_id');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by_user_id');
    }
}
