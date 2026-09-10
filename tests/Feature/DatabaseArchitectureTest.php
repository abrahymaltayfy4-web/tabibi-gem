<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\AccountStatus;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_are_seeded_correctly(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->assertDatabaseHas('roles', ['name' => 'patient']);
        $this->assertDatabaseHas('roles', ['name' => 'doctor']);
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
        $this->assertDatabaseHas('roles', ['name' => 'super_admin']);

        $superAdmin = Role::where('name', 'super_admin')->first();
        $this->assertGreaterThan(0, $superAdmin->permissions()->count());
    }

    public function test_central_user_identity_creates_patient_and_doctor_profiles_correctly(): void
    {
        $user = User::factory()->create([
            'full_name' => 'أحمد علي',
            'phone' => '+967771234567',
            'account_status' => AccountStatus::ACTIVE,
        ]);

        $patient = PatientProfile::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'أحمد',
            'last_name' => 'علي',
        ]);

        $this->assertEquals($user->id, $patient->user->id);
        $this->assertDatabaseHas('users', ['phone' => '+967771234567']);
        $this->assertDatabaseHas('patient_profiles', ['first_name' => 'أحمد']);
    }

    public function test_appointment_locking_and_price_snapshotting_preserves_financial_integrity(): void
    {
        $this->seed(\Database\Seeders\AppointmentTypeSeeder::class);

        $patientUser = User::factory()->create();
        $patient = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create();
        $specialty = Specialty::factory()->create();
        $doctor = DoctorProfile::factory()->create([
            'user_id' => $doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'consultation_price' => 7500.00,
        ]);

        $appointmentType = AppointmentType::where('code', 'video')->first();

        $appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_type_id' => $appointmentType->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'price_snapshot' => $doctor->consultation_price,
            'status' => AppointmentStatus::AWAITING_PAYMENT,
            'locked_until' => now()->addMinutes(10),
        ]);

        $this->assertEquals(7500.00, $appointment->price_snapshot);
        $this->assertEquals(AppointmentStatus::AWAITING_PAYMENT, $appointment->status);

        // Update doctor price, snapshot must remain unchanged
        $doctor->update(['consultation_price' => 10000.00]);
        $this->assertEquals(7500.00, $appointment->fresh()->price_snapshot);
    }
}
