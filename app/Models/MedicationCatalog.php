<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'generic_name',
        'form',
        'strength',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
