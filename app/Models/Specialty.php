<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'code', 'icon_svg', 'is_active'];

    public function primaryDoctors(): HasMany
    {
        return $this->hasMany(DoctorProfile::class, 'primary_specialty_id');
    }
}
