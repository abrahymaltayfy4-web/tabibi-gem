<?php

namespace App\Models;

use App\Shared\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DoctorProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'primary_specialty_id',
        'license_number',
        'verification_status',
        'consultation_price',
        'consultation_duration_minutes',
        'is_active_clinic',
        'bio_ar',
        'bio_en',
        'years_of_experience',
        'rating_avg',
        'rating_count',
    ];

    protected $casts = [
        'verification_status' => VerificationStatus::class,
        'consultation_price' => 'decimal:2',
        'is_active_clinic' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function primarySpecialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class, 'primary_specialty_id');
    }

    public function subSpecialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialties', 'doctor_id', 'specialty_id');
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(DoctorAvailabilityRule::class, 'doctor_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function verificationRequests(): HasMany
    {
        return $this->hasMany(DoctorVerificationRequest::class, 'doctor_id');
    }
}
