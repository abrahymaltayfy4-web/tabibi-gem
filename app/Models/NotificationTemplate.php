<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'title_template_ar',
        'title_template_en',
        'body_template_ar',
        'body_template_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
