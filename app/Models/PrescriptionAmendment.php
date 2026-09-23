<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionAmendment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'original_prescription_id',
        'amended_prescription_id',
        'author_id',
        'reason',
        'snapshot_json',
        'created_at',
    ];

    protected $casts = [
        'snapshot_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function originalPrescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'original_prescription_id');
    }

    public function amendedPrescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'amended_prescription_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
