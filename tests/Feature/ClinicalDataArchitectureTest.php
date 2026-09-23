<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Consultation;
use App\Models\DoctorProfile;
use App\Models\PatientMedicalProfile;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClinicalDataArchitectureTest extends TestCase
{
    use RefreshDatabase;

    protected User $doctorUser;

    protected DoctorProfile $doctorProfile;

    protected User $patientUser;

    protected PatientProfile $patientProfile;

    protected Consultation $consultation;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $specialty = Specialty::create([
            'name_ar' => 'طب الباطنية',
            'name_en' => 'Internal Medicine',
            'code' => 'INT_MED',
        ]);

        $appType = AppointmentType::create([
            'name_ar' => 'استشارة مرئية',
            'name_en' => 'Video Consultation',
            'code' => 'video',
        ]);

        $this->doctorUser = User::factory()->create();
        $this->doctorProfile = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'DOC-CLIN-123',
            'verification_status' => 'Approved',
            'consultation_price' => 20000,
        ]);

        $this->patientUser = User::factory()->create();
        $this->patientProfile = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'أحمد',
            'last_name' => 'علي',
            'gender' => 'Male',
            'date_of_birth' => '1990-05-15',
        ]);

        PatientMedicalProfile::create([
            'patient_id' => $this->patientProfile->id,
            'uuid' => (string) Str::uuid(),
            'blood_type' => 'O+',
            'emergency_contact_name' => 'أحمد علي',
            'emergency_contact_phone' => '770000000',
        ]);

        $appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'patient_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctorProfile->id,
            'appointment_type_id' => $appType->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'price_snapshot' => 20000,
            'status' => 'Confirmed',
        ]);

        $this->consultation = Consultation::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $appointment->id,
            'patient_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctorProfile->id,
            'agora_channel_name' => 'tabibi_channel_'.$appointment->id,
            'session_status' => SessionStatus::ACTIVE->value,
            'started_at' => now(),
        ]);
    }

    public function test_doctor_can_retrieve_patient_medical_profile_and_log_audit(): void
    {
        $response = $this->actingAs($this->doctorUser)
            ->getJson("/api/v1/patients/{$this->patientProfile->id}/medical-profile");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.blood_type', 'O+');

        $this->assertDatabaseHas('medical_access_logs', [
            'actor_id' => $this->doctorUser->id,
            'patient_id' => $this->patientProfile->id,
            'access_context' => 'VIEW_MEDICAL_PROFILE',
        ]);
    }

    public function test_doctor_can_store_comprehensive_clinical_soap_notes_and_diagnoses(): void
    {
        $payload = [
            'chief_complaint' => 'صداع حاد وارتفاع في ضغط الدم منذ ثلاثة أيام',
            'examination_notes' => 'ضغط الدم 140/90، النبض 80 دقيقة',
            'diagnosis_notes' => 'فرط ضغط الدم الأولي',
            'treatment_plan' => 'تقليل الملح وتناول العلاج المناسب',
            'diagnoses' => [
                [
                    'diagnosis_name' => 'Essential Hypertension',
                    'icd_code' => 'I10',
                ],
            ],
            'treatment_plan_details' => [
                'recommendations' => 'متابعة قياس ضغط الدم يومياً وفحص الوظائف بعد شهر',
            ],
        ];

        $response = $this->actingAs($this->doctorUser)
            ->postJson("/api/v1/consultations/{$this->consultation->id}/clinical-notes", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.diagnoses.0.icd_code', 'I10');

        $this->assertDatabaseHas('medical_records', [
            'consultation_id' => $this->consultation->id,
            'chief_complaint' => 'صداع حاد وارتفاع في ضغط الدم منذ ثلاثة أيام',
        ]);

        $this->assertDatabaseHas('diagnoses', [
            'icd_code' => 'I10',
            'diagnosis_name' => 'Essential Hypertension',
        ]);

        $this->assertDatabaseHas('medical_access_logs', [
            'actor_id' => $this->doctorUser->id,
            'patient_id' => $this->patientProfile->id,
            'access_context' => 'CREATE_COMPREHENSIVE_CLINICAL_RECORD',
        ]);
    }

    public function test_uploading_medical_document_validates_and_logs_audit(): void
    {
        $file = UploadedFile::fake()->create('blood_test.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->doctorUser)
            ->postJson("/api/v1/patients/{$this->patientProfile->id}/medical-documents", [
                'file' => $file,
                'document_type' => 'lab_test',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.document_type', 'lab_test');

        $this->assertDatabaseHas('medical_documents', [
            'patient_id' => $this->patientProfile->id,
            'uploaded_by_user_id' => $this->doctorUser->id,
            'document_type' => 'lab_test',
            'original_filename' => 'blood_test.pdf',
        ]);

        $this->assertDatabaseHas('medical_access_logs', [
            'actor_id' => $this->doctorUser->id,
            'patient_id' => $this->patientProfile->id,
            'access_context' => 'UPLOAD_MEDICAL_DOCUMENT',
        ]);
    }
}
