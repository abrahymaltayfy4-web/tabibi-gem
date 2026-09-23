<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition(): array
    {
        $code = 'SPEC_'.strtoupper($this->faker->unique()->lexify('???'));

        return [
            'name_ar' => 'تخصص '.$this->faker->word(),
            'name_en' => 'Specialty '.$this->faker->word(),
            'code' => $code,
            'is_active' => true,
        ];
    }
}
