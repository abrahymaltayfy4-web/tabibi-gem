<?php

namespace Tests\Feature;

use App\Models\AppointmentType;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\AppointmentStatus;
use App\Shared\Enums\PaymentStatus;
use App\Shared\Enums\VerificationStatus;
use Database\Seeders\AppointmentTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SpecialtySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->seed(SpecialtySeeder::class);
        $this->seed(AppointmentTypeSeeder::class);
    }

    public function test_public_can_discover_approved_doctors(): void
    {
        $doctorUser = User::factory()->create(['full_name' => 'د. أنس الهلالي']);
        $specialty = Specialty::first();
        DoctorProfile::factory()->create([
            'user_id' => $doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'verification_status' => VerificationStatus::APPROVED,
            'is_active_clinic' => true,
        ]);

        $response = $this->getJson('/api/v1/doctors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'license_number', 'consultation_price_yer', 'primary_specialty'],
                ],
            ]);
    }

    public function test_patient_can_book_appointment_and_complete_yer_payment(): void
    {
        // 1. Create Patient User & Profile
        $patientUser = User::factory()->create();
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        // 2. Create Approved Doctor
        $doctorUser = User::factory()->create();
        $specialty = Specialty::first();
        $doctorProfile = DoctorProfile::factory()->create([
            'user_id' => $doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'verification_status' => VerificationStatus::APPROVED,
            'consultation_price' => 8000.00,
            'is_active_clinic' => true,
        ]);

        $appointmentType = AppointmentType::where('code', 'video')->first();

        // 3. Book Appointment
        $bookingResponse = $this->actingAs($patientUser, 'sanctum')
            ->postJson('/api/v1/appointments/book', [
                'doctor_id' => $doctorProfile->id,
                'appointment_type_id' => $appointmentType->id,
                'appointment_date' => now()->addDays(2)->format('Y-m-d'),
                'start_time' => '11:00',
                'notes' => 'صداع مستمر وسخونة',
            ]);

        $bookingResponse->assertStatus(201)
            ->assertJsonPath('data.status', 'AwaitingPayment')
            ->assertJsonPath('data.price_snapshot_yer', 8000);

        $appointmentId = $bookingResponse->json('data.id');

        // 4. Initiate Payment Checkout
        $checkoutResponse = $this->actingAs($patientUser, 'sanctum')
            ->postJson("/api/v1/payments/checkout/{$appointmentId}");

        $checkoutResponse->assertStatus(200);
        $txRef = $checkoutResponse->json('data.transaction_reference');

        // 5. Complete Sandboxed YER Payment Callback
        $callbackResponse = $this->actingAs($patientUser, 'sanctum')
            ->postJson("/api/v1/payments/sandbox-callback/{$txRef}", [
                'sandbox_user' => 'patient_1',
            ]);

        $callbackResponse->assertStatus(200)
            ->assertJsonPath('data.status', PaymentStatus::SUCCEEDED->value)
            ->assertJsonPath('data.appointment_status', 'Confirmed');

        $this->assertDatabaseHas('payments', [
            'transaction_reference' => $txRef,
            'payment_status' => PaymentStatus::SUCCEEDED->value,
        ]);

        $this->assertDatabaseHas('appointments', [
            'id' => $appointmentId,
            'status' => AppointmentStatus::CONFIRMED->value,
        ]);

        $this->assertDatabaseHas('doctor_earnings', [
            'appointment_id' => $appointmentId,
            'gross_amount' => 8000.00,
        ]);
    }

    public function test_admin_can_approve_doctor_verification(): void
    {
        $adminUser = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->roles()->attach($adminRole->id);

        $doctorUser = User::factory()->create();
        $specialty = Specialty::first();
        $doctorProfile = DoctorProfile::factory()->create([
            'user_id' => $doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'verification_status' => VerificationStatus::PENDING,
        ]);

        $approveResponse = $this->actingAs($adminUser, 'sanctum')
            ->postJson("/api/v1/admin/verifications/{$doctorProfile->id}/approve");

        $approveResponse->assertStatus(200)
            ->assertJsonPath('data.verification_status', 'Approved');

        $this->assertDatabaseHas('doctor_profiles', [
            'id' => $doctorProfile->id,
            'verification_status' => VerificationStatus::APPROVED->value,
        ]);
    }
}
