<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\DTOs\RegisterPatientDTO;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\User;
use App\Shared\Enums\AccountStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterPatientAction
{
    public function execute(RegisterPatientDTO $dto): array
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'full_name' => $dto->fullName,
                'email' => $dto->email,
                'phone' => $dto->phone,
                'password_hash' => Hash::make($dto->password),
                'account_status' => AccountStatus::ACTIVE,
                'phone_verified_at' => now(),
            ]);

            $patientRole = Role::where('name', 'patient')->first();
            if ($patientRole) {
                $user->roles()->attach($patientRole->id);
            }

            $nameParts = explode(' ', trim($dto->fullName), 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            $profile = PatientProfile::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $dto->dateOfBirth,
                'gender' => $dto->gender,
                'blood_group' => $dto->bloodGroup,
            ]);

            $token = $user->createToken('patient_auth_token')->plainTextToken;

            return [
                'user' => $user->load(['patientProfile', 'roles']),
                'token' => $token,
            ];
        });
    }
}
