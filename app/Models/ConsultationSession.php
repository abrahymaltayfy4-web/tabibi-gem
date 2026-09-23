<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'consultation_id',
        'event_type',
        'event_data_json',
        'recorded_by_user_id',
        'created_at',
    ];

    protected $casts = [
        'event_data_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
