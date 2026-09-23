<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecommendationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'doctor_id',
        'match_score',
        'match_reasons_json',
        'rank_position',
    ];

    protected $casts = [
        'match_score' => 'float',
        'match_reasons_json' => 'array',
        'rank_position' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(RecommendationRequest::class, 'request_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }
}
