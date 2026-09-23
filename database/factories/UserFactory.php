<?php

namespace Database\Factories;

use App\Models\User;
use App\Shared\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '+967'.$this->faker->unique()->numerify('77#######'),
            'password_hash' => bcrypt('password123'),
            'account_status' => AccountStatus::ACTIVE,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ];
    }
}
