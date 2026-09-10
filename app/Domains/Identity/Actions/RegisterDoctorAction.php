<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\DTOs\RegisterDoctorDTO;
use App\Models\DoctorProfile;
use App\Models\Role;
use App\Models\User;
use App\Shared\Enums\AccountStatus;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterDoctorAction
{
    public function execute(RegisterDoctorDTO $dto): array
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

            $doctorRole = Role::where('name', 'doctor')->first();
            if ($doctorRole) {
                $user->roles()->attach($doctorRole->id);
            }

            $profile = DoctorProfile::create([
                'user_id' => $user->id,
                'primary_specialty_id' => $dto->primarySpecialtyId,
                'license_number' => $dto->licenseNumber,
                'verification_status' => VerificationStatus::PENDING,
                'consultation_price' => $dto->consultationPrice,
                'bio_ar' => $dto->bioAr,
                'years_of_experience' => $dto->yearsOfExperience,
                'is_active_clinic' => true,
            ]);

            $token = $user->createToken('doctor_auth_token')->plainTextToken;

            return [
                'user' => $user->load(['doctorProfile.primarySpecialty', 'roles']),
                'token' => $token,
            ];
        });
    }
}
