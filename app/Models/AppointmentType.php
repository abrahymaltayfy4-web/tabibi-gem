<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentType extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'code'];
}
