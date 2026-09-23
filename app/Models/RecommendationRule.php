<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RecommendationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'symptom_keyword',
        'specialty_code',
        'weight',
        'is_active',
        'rule_version',
    ];

    protected $casts = [
        'weight' => 'float',
        'is_active' => 'boolean',
        'rule_version' => 'integer',
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
}
