<?php

namespace Tests\Feature;

use App\Enums\PrescriptionState;
use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Consultation;
use App\Models\DoctorProfile;
use App\Models\MedicalRecord;
use App\Models\PatientProfile;
use App\Models\Prescription;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MedicalRecordPrescriptionTest extends TestCase
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
            'license_number' => 'DOC-998877',
            'verification_status' => 'Approved',
            'consultation_price' => 15000,
        ]);

        $this->patientUser = User::factory()->create();
        $this->patientProfile = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'أحمد',
            'last_name' => 'علي',
            'gender' => 'Male',
        ]);

        $appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'patient_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctorProfile->id,
            'appointment_type_id' => $appType->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'Confirmed',
            'price_snapshot' => 15000,
        ]);

        $this->consultation = Consultation::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $appointment->id,
            'patient_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctorProfile->id,
            'agora_channel_name' => 'tabibi_channel_test_123',
            'session_status' => SessionStatus::ACTIVE->value,
        ]);
    }

    public function test_doctor_can_create_soap_medical_record_with_symptoms()
    {
        $response = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/medical-records", [
                'chief_complaint' => 'صداع شديد وارتفاع في درجة الحرارة',
                'examination_notes' => 'الضغط 120/80 والحرارة 38.5',
                'diagnosis_notes' => 'التهاب حاد في الحلق',
                'treatment_plan' => 'راحة تامة وتناول الخافضات للمواظبة',
                'symptoms' => [
                    [
                        'name' => 'صداع',
                        'severity' => 'severe',
                        'duration' => 'يومان',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.chief_complaint', 'صداع شديد وارتفاع في درجة الحرارة');

        $this->assertDatabaseHas('medical_records', [
            'consultation_id' => $this->consultation->id,
            'patient_id' => $this->patientProfile->id,
        ]);

        $this->assertDatabaseHas('symptoms', [
            'name' => 'صداع',
            'severity' => 'severe',
        ]);

        $this->assertDatabaseHas('medical_access_logs', [
            'actor_id' => $this->doctorUser->id,
            'access_context' => 'CREATE_MEDICAL_RECORD',
        ]);
    }

    public function test_doctor_can_amend_medical_record_creating_version_snapshot()
    {
        $record = MedicalRecord::create([
            'uuid' => (string) Str::uuid(),
            'consultation_id' => $this->consultation->id,
            'patient_id' => $this->patientProfile->id,
            'doctor_id' => $this->doctorProfile->id,
            'chief_complaint' => 'ألم بالبطن',
            'diagnosis_notes' => 'عسر هضم بسيط',
        ]);

        $response = $this->actingAs($this->doctorUser, 'sanctum')
            ->putJson("/api/v1/medical-records/{$record->id}", [
                'change_reason' => 'تصحيح التشخيص بعد ظهور نتائج التحاليل',
                'diagnosis_notes' => 'التهاب بسيط بالمعدة',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.diagnosis_notes', 'التهاب بسيط بالمعدة');

        $this->assertDatabaseHas('medical_record_versions', [
            'medical_record_id' => $record->id,
            'version_number' => 1,
            'diagnosis_notes' => 'عسر هضم بسيط',
            'change_reason' => 'تصحيح التشخيص بعد ظهور نتائج التحاليل',
        ]);
    }

    public function test_prescription_lifecycle_draft_finalize_and_amend()
    {
        // 1. Create Draft Prescription
        $draftRes = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/prescriptions", [
                'notes' => 'تناول الأدوية بعد الطعام',
                'is_finalized' => false,
                'items' => [
                    [
                        'medication_name' => 'Paracetamol',
                        'dosage' => '500mg',
                        'frequency' => '3 مرات يومياً',
                        'duration' => '5 أيام',
                    ],
                ],
            ]);

        $draftRes->assertStatus(201);
        $prescriptionId = $draftRes->json('data.id');

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescriptionId,
            'status' => 'Draft',
        ]);

        // 2. Finalize Prescription
        $finalizeRes = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/prescriptions/{$prescriptionId}/finalize");

        $finalizeRes->assertStatus(200)
            ->assertJsonPath('data.status', 'Finalized');

        $this->assertNotNull(Prescription::find($prescriptionId)->prescription_number);

        // 3. Amend Finalized Prescription
        $amendRes = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/prescriptions/{$prescriptionId}/amend", [
                'reason' => 'تعديل جرعة البندول لشدة الحرارة',
                'items' => [
                    [
                        'medication_name' => 'Paracetamol',
                        'dosage' => '1000mg',
                        'frequency' => '3 مرات يومياً',
                        'duration' => '5 أيام',
                    ],
                ],
            ]);

        $amendRes->assertStatus(200)
            ->assertJsonPath('data.status', 'Finalized');

        // Original prescription is marked as Amended
        $this->assertEquals(PrescriptionState::Amended, Prescription::find($prescriptionId)->status);

        $this->assertDatabaseHas('prescription_amendments', [
            'original_prescription_id' => $prescriptionId,
            'reason' => 'تعديل جرعة البندول لشدة الحرارة',
        ]);
    }

    public function test_longitudinal_patient_medical_history()
    {
        $response = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/patients/{$this->patientProfile->id}/longitudinal-history", [
                'category' => 'allergy',
                'title' => 'حساسية البنسلين',
                'description' => 'طفح جلدي عند تناول مركبات البنسلين',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'حساسية البنسلين');

        $this->assertDatabaseHas('patient_medical_histories', [
            'patient_id' => $this->patientProfile->id,
            'category' => 'allergy',
            'title' => 'حساسية البنسلين',
        ]);
    }

    public function test_unauthorized_doctor_cannot_view_unrelated_patient_medical_history()
    {
        $otherDoctorUser = User::factory()->create();

        $response = $this->actingAs($otherDoctorUser, 'sanctum')
            ->getJson("/api/v1/patients/{$this->patientProfile->id}/medical-history");

        $response->assertStatus(422)
            ->assertJsonPath('errors.authorization.0', 'غير مصرح لك بالاطلاع على السجل الطبي لهذا المريض.');
    }
}
