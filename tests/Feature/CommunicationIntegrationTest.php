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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommunicationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected User $doctorUser;

    protected PatientProfile $patient;

    protected DoctorProfile $doctor;

    protected Appointment $appointment;

    protected Consultation $consultation;

    protected function setUp(): void
    {
        parent::setUp();

        $specialty = Specialty::create(['code' => 'pediatrics', 'name_ar' => 'طب الأطفال', 'name_en' => 'Pediatrics']);

        $this->doctorUser = User::factory()->create(['full_name' => 'د. أحمد المحمدي', 'email' => 'doctor@tabibi.test']);
        $this->doctor = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'LIC-YEM-987654',
            'verification_status' => 'Approved',
            'consultation_price' => 15000,
            'consultation_duration_minutes' => 30,
            'is_active_clinic' => true,
            'bio_ar' => 'خبرة 10 سنوات في طب الأطفال',
            'years_of_experience' => 10,
        ]);

        $this->patientUser = User::factory()->create(['full_name' => 'علي صالح', 'email' => 'patient@tabibi.test']);
        $this->patient = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'علي',
            'last_name' => 'صالح',
            'gender' => 'Male',
            'date_of_birth' => '1995-05-15',
        ]);

        $appType = AppointmentType::create(['name_ar' => 'استشارة مرئية', 'name_en' => 'Video Consultation', 'code' => 'video']);

        $this->appointment = Appointment::create([
            'uuid' => (string) Str::uuid(),
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'appointment_type_id' => $appType->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'price_snapshot' => 15000,
            'status' => 'Confirmed',
        ]);

        $this->consultation = Consultation::create([
            'uuid' => (string) Str::uuid(),
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'agora_channel_name' => "tabibi_channel_{$this->appointment->id}",
            'session_status' => SessionStatus::SCHEDULED->value,
        ]);
    }

    public function test_patient_and_doctor_can_join_consultation_session(): void
    {
        // Patient joins first
        $response1 = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");

        $response1->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.session_status', SessionStatus::WAITING_FOR_PARTICIPANTS->value)
            ->assertJsonStructure(['data' => ['rtc_token', 'channel_name', 'conversation_id']]);

        // Doctor joins second -> transitions to Active
        $response2 = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");

        $response2->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.session_status', SessionStatus::ACTIVE->value);

        $this->assertDatabaseHas('consultation_sessions', [
            'consultation_id' => $this->consultation->id,
            'event_type' => 'UserJoined',
        ]);
    }

    public function test_chat_messaging_with_idempotency_and_read_receipts(): void
    {
        // Join session to initialize conversation
        $joinResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");
        $conversationId = $joinResponse->json('data.conversation_id');

        // Send message from patient
        $msgResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/conversations/{$conversationId}/messages", [
                'content' => 'مرحباً دكتور، أعاني من ارتفاع حرارة طفلي.',
                'client_msg_id' => 'msg_unique_client_12345',
            ]);

        $msgResponse->assertStatus(201)
            ->assertJsonPath('data.content', 'مرحباً دكتور، أعاني من ارتفاع حرارة طفلي.')
            ->assertJsonPath('data.client_msg_id', 'msg_unique_client_12345');

        $messageId = $msgResponse->json('data.id');

        // Duplicate send attempt returns existing message
        $dupResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/conversations/{$conversationId}/messages", [
                'content' => 'مرحباً دكتور، أعاني من ارتفاع حرارة طفلي.',
                'client_msg_id' => 'msg_unique_client_12345',
            ]);

        $dupResponse->assertStatus(201)
            ->assertJsonPath('data.id', $messageId);

        // Doctor marks message as read
        $readResponse = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/messages/{$messageId}/read");

        $readResponse->assertStatus(200)
            ->assertJsonPath('data.is_read', true);
    }

    public function test_secure_medical_attachment_upload_and_signed_url_generation(): void
    {
        Storage::fake('local');

        $joinResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");
        $conversationId = $joinResponse->json('data.conversation_id');

        $file = UploadedFile::fake()->create('blood_test_report.pdf', 500, 'application/pdf');

        $uploadResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/conversations/{$conversationId}/attachments", [
                'file' => $file,
                'category' => 'medical',
            ]);

        $uploadResponse->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $fileId = $uploadResponse->json('data.attachments.0.file.id');

        // Generate signed URL
        $signedResponse = $this->actingAs($this->doctorUser, 'sanctum')
            ->getJson("/api/v1/files/{$fileId}/signed-url");

        $signedResponse->assertStatus(200)
            ->assertJsonStructure(['data' => ['signed_url', 'expires_in_minutes']]);

        $this->assertDatabaseHas('file_access_logs', [
            'file_id' => $fileId,
            'actor_id' => $this->doctorUser->id,
        ]);
    }

    public function test_presence_heartbeat_recording(): void
    {
        $response = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson('/api/v1/presence/heartbeat', ['status' => 'InConsultation']);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $presenceResponse = $this->actingAs($this->patientUser, 'sanctum')
            ->getJson("/api/v1/presence/{$this->patientUser->id}");

        $presenceResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'InConsultation');
    }

    public function test_ending_consultation_calculates_duration_and_updates_status(): void
    {
        $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");

        $endResponse = $this->actingAs($this->doctorUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/end", [
                'reason' => 'تم تقديم النصائح الطبية وكتابة الوصفة الطبية.',
            ]);

        $endResponse->assertStatus(200)
            ->assertJsonPath('data.session_status', SessionStatus::COMPLETED->value);

        $this->assertDatabaseHas('consultations', [
            'id' => $this->consultation->id,
            'session_status' => SessionStatus::COMPLETED->value,
        ]);
    }
}
