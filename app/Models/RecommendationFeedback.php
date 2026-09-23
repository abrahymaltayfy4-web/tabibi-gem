<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationFeedback extends Model
{
    use HasFactory;

    protected $table = 'recommendation_feedbacks';

    protected $fillable = [
        'request_id',
        'patient_id',
        'is_accepted',
        'selected_doctor_id',
        'utility_score',
        'feedback_notes',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(RecommendationRequest::class, 'request_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function selectedDoctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'selected_doctor_id');
    }
}
