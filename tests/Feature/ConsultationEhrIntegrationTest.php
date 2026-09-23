<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Consultation;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConsultationEhrIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected User $doctorUser;

    protected User $unauthorizedUser;

    protected PatientProfile $patient;

    protected DoctorProfile $doctor;

    protected Appointment $appointment;

    protected Consultation $consultation;

    protected function setUp(): void
    {
        parent::setUp();

        $specialty = Specialty::create(['code' => 'internal', 'name_ar' => 'الباطنية', 'name_en' => 'Internal Medicine']);

        $this->doctorUser = User::factory()->create(['full_name' => 'د. ياسر العبسي', 'email' => 'doctor.yasser@tabibi.test']);
        $this->doctor = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'LIC-YEM-778899',
            'verification_status' => 'Approved',
            'consultation_price' => 18000,
            'consultation_duration_minutes' => 30,
            'is_active_clinic' => true,
        ]);

        $this->patientUser = User::factory()->create(['full_name' => 'فؤاد سالم', 'email' => 'patient.fouad@tabibi.test']);
        $this->patient = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'فؤاد',
            'last_name' => 'سالم',
            'gender' => 'Male',
            'date_of_birth' => '1988-03-20',
        ]);

        $this->unauthorizedUser = User::factory()->create(['full_name' => 'مستخدم غير مخول', 'email' => 'unauthorized@tabibi.test']);

        $appType = AppointmentType::create(['name_ar' => 'استشارة مرئية', 'name_en' => 'Video Consultation', 'code' => 'video']);

        $this->appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'appointment_type_id' => $appType->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '11:00:00',
            'end_time' => '11:30:00',
            'price_snapshot' => 18000,
            'status' => 'Confirmed',
        ]);

        $this->consultation = Consultation::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'agora_channel_name' => "tabibi_channel_ehr_{$this->appointment->id}",
            'session_status' => SessionStatus::COMPLETED->value,
        ]);
    }

    public function test_doctor_can_create_clinical_medical_record_for_consultation(): void
    {
        $response = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/medical-records", [
                'chief_complaint' => 'ألم حاد في المعدة مع غثيان المستمر.',
                'examination_notes' => 'الفحص السريري يظهر حساسية بالضغط في الشرسوف.',
                'diagnosis_notes' => 'التهاب حاد في غشاء المعدة (Acute Gastritis).',
                'treatment_plan' => 'حمية غذائية خفيفة مع دواء أوميبرازول 20mg.',
                'is_finalized' => true,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.diagnosis_notes', 'التهاب حاد في غشاء المعدة (Acute Gastritis).');

        $this->assertDatabaseHas('medical_records', [
            'consultation_id' => $this->consultation->id,
            'patient_id' => $this->patient->id,
        ]);
    }

    public function test_doctor_can_issue_e_prescription_with_medication_items(): void
    {
        $response = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/prescriptions", [
                'notes' => 'تناول الأدوية بعد الوجبات مباشرة مع شرب كمية كافية من الماء.',
                'items' => [
                    [
                        'medication_name' => 'Omeprazole 20mg',
                        'dosage' => 'كبسولة واحدة',
                        'frequency' => 'مرتين يومياً',
                        'duration' => '14 يوم',
                        'instructions' => 'قبل الأكل بـ 30 دقيقة',
                    ],
                    [
                        'medication_name' => 'Antacid Syrup',
                        'dosage' => '10 مل',
                        'frequency' => 'عند الحاجة',
                        'duration' => '7 أيام',
                        'instructions' => 'بعد الأكل وعند الشعور بالحرقة',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data.items');

        $this->assertDatabaseHas('prescriptions', [
            'consultation_id' => $this->consultation->id,
            'patient_id' => $this->patient->id,
        ]);

        $this->assertDatabaseHas('prescription_items', [
            'medication_name' => 'Omeprazole 20mg',
        ]);
    }

    public function test_patient_can_view_own_medical_history_and_prescriptions(): void
    {
        // Setup record
        $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/medical-records", [
                'diagnosis_notes' => 'التهاب المعدة',
            ]);

        // Patient fetches medical history
        $historyResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->getJson("/api/v1/patients/{$this->patient->id}/medical-history");

        $historyResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');
    }

    public function test_unauthorized_user_cannot_view_other_patient_medical_history(): void
    {
        $response = $this->actingAs($this->unauthorizedUser, 'sanctum')
            ->getJson("/api/v1/patients/{$this->patient->id}/medical-history");

        $response->assertStatus(422);
    }
}
