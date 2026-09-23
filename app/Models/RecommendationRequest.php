<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecommendationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'symptoms_description',
        'category_code',
        'max_price_yer',
        'preferred_gender',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(RecommendationResult::class, 'request_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(RecommendationFeedback::class, 'request_id');
    }
}
