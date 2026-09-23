<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PatientMedicalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'patient_id',
        'blood_type',
        'height_cm',
        'weight_kg',
        'smoking_status',
        'alcohol_status',
        'medical_notes',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(PatientAllergy::class, 'patient_medical_profile_id');
    }

    public function chronicConditions(): HasMany
    {
        return $this->hasMany(PatientChronicCondition::class, 'patient_medical_profile_id');
    }
}
