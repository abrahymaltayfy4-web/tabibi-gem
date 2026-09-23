<?php

namespace Database\Seeders;

use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\AccountStatus;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        $patientRole = Role::where('name', 'patient')->first();
        $specialty = Specialty::first();

        // 1. Super Admin Accounts
        $adminAccounts = [
            ['email' => 'admin@tabibi.com', 'phone' => '770000000'],
            ['email' => 'admin@tabibi.ye', 'phone' => '770000001'],
        ];

        foreach ($adminAccounts as $data) {
            $superAdmin = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'uuid' => (string) Str::uuid(),
                    'full_name' => 'مدير المنصة (Super Admin)',
                    'phone' => $data['phone'],
                    'password_hash' => Hash::make('password123'),
                    'account_status' => AccountStatus::ACTIVE,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );

            if ($superAdminRole && !$superAdmin->roles()->where('name', 'super_admin')->exists()) {
                $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
            }
        }

        // 2. Doctor Accounts
        $doctorAccounts = [
            ['email' => 'doctor@tabibi.com', 'phone' => '771111111', 'license' => 'DOC-99201-YE'],
            ['email' => 'doctor@tabibi.ye', 'phone' => '771111112', 'license' => 'DOC-99202-YE'],
        ];

        foreach ($doctorAccounts as $index => $data) {
            $doctorUser = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'uuid' => (string) Str::uuid(),
                    'full_name' => 'د. أحمد المحمدي',
                    'phone' => $data['phone'],
                    'password_hash' => Hash::make('password123'),
                    'account_status' => AccountStatus::ACTIVE,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );

            if ($doctorRole && !$doctorUser->roles()->where('name', 'doctor')->exists()) {
                $doctorUser->roles()->syncWithoutDetaching([$doctorRole->id]);
            }

            if ($doctorUser && !$doctorUser->doctorProfile) {
                DoctorProfile::create([
                    'user_id' => $doctorUser->id,
                    'primary_specialty_id' => $specialty?->id ?? 1,
                    'license_number' => $data['license'],
                    'verification_status' => VerificationStatus::APPROVED,
                    'consultation_price' => 5000.00,
                    'consultation_duration_minutes' => 30,
                    'is_active_clinic' => true,
                    'bio_ar' => 'استشاري الباطنية والقلب بخبرة 10 سنوات في الرعاية الطبية الرقمية.',
                    'years_of_experience' => 10,
                    'rating_avg' => 4.90,
                    'rating_count' => 24,
                ]);
            }

            if ($doctorUser && $doctorUser->doctorProfile) {
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $dayName) {
                    \App\Models\DoctorAvailabilityRule::firstOrCreate([
                        'doctor_id' => $doctorUser->doctorProfile->id,
                        'day_of_week' => $dayName,
                    ], [
                        'start_time' => '08:00:00',
                        'end_time' => '22:00:00',
                        'slot_duration_minutes' => 30,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // 3. Patient Accounts
        $patientAccounts = [
            ['email' => 'patient@tabibi.com', 'phone' => '772222222'],
            ['email' => 'patient@tabibi.ye', 'phone' => '772222223'],
        ];

        foreach ($patientAccounts as $data) {
            $patientUser = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'uuid' => (string) Str::uuid(),
                    'full_name' => 'علي عبد الله',
                    'phone' => $data['phone'],
                    'password_hash' => Hash::make('password123'),
                    'account_status' => AccountStatus::ACTIVE,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );

            if ($patientRole && !$patientUser->roles()->where('name', 'patient')->exists()) {
                $patientUser->roles()->syncWithoutDetaching([$patientRole->id]);
            }

            if ($patientUser && !$patientUser->patientProfile) {
                PatientProfile::create([
                    'user_id' => $patientUser->id,
                    'first_name' => 'علي',
                    'last_name' => 'عبد الله',
                    'date_of_birth' => '1995-05-15',
                    'gender' => 'Male',
                    'blood_group' => 'O+',
                ]);
            }
        }
    }
}
