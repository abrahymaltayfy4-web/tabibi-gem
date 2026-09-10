<?php

namespace Database\Factories;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientProfileFactory extends Factory
{
    protected $model = PatientProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-20 years'),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'blood_group' => $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+']),
        ];
    }
}
