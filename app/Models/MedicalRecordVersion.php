<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'medical_record_id',
        'author_id',
        'version_number',
        'chief_complaint',
        'examination_notes',
        'diagnosis_notes',
        'treatment_plan',
        'change_reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
