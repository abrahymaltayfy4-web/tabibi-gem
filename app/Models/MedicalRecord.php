<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'patient_id',
        'doctor_id',
        'consultation_id',
        'chief_complaint',
        'examination_notes',
        'diagnosis_notes',
        'treatment_plan',
        'is_finalized',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MedicalRecordVersion::class, 'medical_record_id');
    }

    public function symptoms(): HasMany
    {
        return $this->hasMany(Symptom::class, 'medical_record_id');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class, 'medical_record_id');
    }

    public function treatmentPlan(): HasOne
    {
        return $this->hasOne(TreatmentPlan::class, 'medical_record_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(MedicalDocument::class, 'medical_record_id');
    }
}
