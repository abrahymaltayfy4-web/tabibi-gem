<?php

namespace Tests\Feature;

use App\Models\DoctorProfile;
use App\Models\PatientMedicalProfile;
use App\Models\PatientProfile;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityPrivacyAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientA;

    protected PatientProfile $patientProfileA;

    protected User $patientB;

    protected PatientProfile $patientProfileB;

    protected User $doctorUser;

    protected DoctorProfile $doctorProfile;

    protected User $adminUser;

    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Patient A
        $this->patientA = User::factory()->create(['full_name' => 'المريض أ']);
        $this->patientProfileA = PatientProfile::create([
            'user_id' => $this->patientA->id,
            'first_name' => 'المريض',
            'last_name' => 'أ',
            'gender' => 'Male',
        ]);
        PatientMedicalProfile::create([
            'patient_id' => $this->patientProfileA->id,
            'uuid' => (string) Str::uuid(),
            'blood_type' => 'O+',
        ]);

        // 2. Setup Patient B
        $this->patientB = User::factory()->create(['full_name' => 'المريض ب']);
        $this->patientProfileB = PatientProfile::create([
            'user_id' => $this->patientB->id,
            'first_name' => 'المريض',
            'last_name' => 'ب',
            'gender' => 'Female',
        ]);
        PatientMedicalProfile::create([
            'patient_id' => $this->patientProfileB->id,
            'uuid' => (string) Str::uuid(),
            'blood_type' => 'A+',
        ]);

        // 3. Setup Doctor
        $specialty = Specialty::create(['code' => 'GEN', 'name_ar' => 'عام', 'name_en' => 'General']);
        $this->doctorUser = User::factory()->create(['full_name' => 'د. خالد الحسام']);
        $this->doctorProfile = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'DOC-SEC-99',
            'verification_status' => VerificationStatus::PENDING,
            'consultation_price' => 15000,
        ]);

        // 4. Setup Admin Role and User
        $this->adminRole = Role::create([
            'name' => 'Admin',
            'guard_name' => 'web',
            'description' => 'مدير المنصة',
        ]);

        $this->adminUser = User::factory()->create(['full_name' => 'المشرف العام']);
        $this->adminUser->roles()->attach($this->adminRole->id);
    }

    public function test_patient_cannot_access_another_patients_medical_profile_idor_prevention(): void
    {
        // Patient A attempts to read Patient B's medical profile
        $response = $this->actingAs($this->patientA)
            ->getJson("/api/v1/patients/{$this->patientProfileB->id}/medical-profile");

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error');
    }

    public function test_patient_can_access_their_own_medical_profile(): void
    {
        $response = $this->actingAs($this->patientA)
            ->getJson("/api/v1/patients/{$this->patientProfileA->id}/medical-profile");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.blood_type', 'O+');
    }

    public function test_non_admin_users_are_forbidden_from_admin_endpoints(): void
    {
        // Patient A attempts to view pending doctor verifications
        $response = $this->actingAs($this->patientA)
            ->getJson('/api/v1/admin/verifications/pending');

        $response->assertStatus(403)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'غير مصرح لك بالوصول لمركز التحكم الإداري.');

        // Unverified Doctor attempts to view admin endpoints
        $docRes = $this->actingAs($this->doctorUser)
            ->getJson('/api/v1/admin/verifications/pending');

        $docRes->assertStatus(403);
    }

    public function test_authorized_admin_can_access_admin_endpoints_and_action_generates_audit_log(): void
    {
        // Admin views pending verifications
        $viewRes = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/admin/verifications/pending');

        $viewRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Admin approves doctor verification
        $approveRes = $this->actingAs($this->adminUser)
            ->postJson("/api/v1/admin/verifications/{$this->doctorProfile->id}/approve");

        $approveRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $this->adminUser->id,
            'action' => 'DOCTOR_VERIFICATION_APPROVED',
            'entity_type' => DoctorProfile::class,
            'entity_id' => $this->doctorProfile->id,
        ]);
    }
}
