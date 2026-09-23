<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorProfileFactory extends Factory
{
    protected $model = DoctorProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'primary_specialty_id' => Specialty::factory(),
            'license_number' => 'YEM-LIC-'.$this->faker->unique()->numerify('#####'),
            'verification_status' => VerificationStatus::APPROVED,
            'consultation_price' => 5000.00,
            'consultation_duration_minutes' => 30,
            'is_active_clinic' => true,
            'years_of_experience' => $this->faker->numberBetween(3, 20),
            'rating_avg' => 5.00,
            'rating_count' => 10,
        ];
    }
}
