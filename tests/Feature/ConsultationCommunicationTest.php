<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\AppointmentType;
use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Specialty;
use App\Models\User;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConsultationCommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $patientUser;

    protected PatientProfile $patientProfile;

    protected User $doctorUser;

    protected DoctorProfile $doctorProfile;

    protected Consultation $consultation;

    protected Conversation $conversation;

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

        $this->doctorUser = User::factory()->create(['full_name' => 'د. خالد يحيى']);
        $this->doctorProfile = DoctorProfile::create([
            'user_id' => $this->doctorUser->id,
            'primary_specialty_id' => $specialty->id,
            'license_number' => 'DOC-771122',
            'verification_status' => 'Approved',
            'consultation_price' => 15000,
        ]);

        $this->patientUser = User::factory()->create(['full_name' => 'عمر العولقي']);
        $this->patientProfile = PatientProfile::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'عمر',
            'last_name' => 'العولقي',
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
            'agora_channel_name' => 'tabibi_rtc_channel_7788',
            'session_status' => SessionStatus::SCHEDULED->value,
        ]);

        $this->conversation = Conversation::create([
            'consultation_id' => $this->consultation->id,
            'title' => 'محادثة استشارة عمر العولقي',
            'is_active' => true,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->patientUser->id,
            'role_in_chat' => 'patient',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->doctorUser->id,
            'role_in_chat' => 'doctor',
        ]);
    }

    public function test_authorized_patient_can_join_consultation_session_and_log_event()
    {
        $response = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");

        $response->assertStatus(200)
            ->assertJsonPath('data.channel_name', 'tabibi_rtc_channel_7788')
            ->assertJsonPath('data.role', 'patient');

        $this->assertNotNull($response->json('data.rtc_token'));

        $this->assertDatabaseHas('consultation_events', [
            'consultation_id' => $this->consultation->id,
            'actor_id' => $this->patientUser->id,
            'event_name' => 'ParticipantJoined',
        ]);
    }

    public function test_unauthorized_user_cannot_join_other_consultation_session()
    {
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($unauthorizedUser, 'sanctum')
            ->postJson("/api/v1/consultations/{$this->consultation->id}/join");

        $response->assertStatus(422)
            ->assertJsonPath('errors.authorization.0', 'أنت غير مصرح لك بالانضمام لهذه الجلسة الطبية.');
    }

    public function test_chat_message_sending_supports_idempotency_client_msg_id()
    {
        $clientMsgId = 'client-uuid-998877';

        // 1. First Send
        $res1 = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/conversations/{$this->conversation->id}/messages", [
                'client_msg_id' => $clientMsgId,
                'content' => 'مرحباً دكتور، أشعر بتحسن بسيط اليوم',
            ]);

        $res1->assertStatus(201)
            ->assertJsonPath('status', 'success');

        // 2. Duplicate Send with same client_msg_id (Idempotency retry)
        $res2 = $this->actingAs($this->patientUser, 'sanctum')
            ->postJson("/api/v1/conversations/{$this->conversation->id}/messages", [
                'client_msg_id' => $clientMsgId,
                'content' => 'مرحباً دكتور، أشعر بتحسن بسيط اليوم',
            ]);

        $res2->assertStatus(201)
            ->assertJsonPath('data.client_msg_id', $clientMsgId);

        // Ensure database only contains 1 message row
        $this->assertDatabaseCount('messages', 1);
    }
}
