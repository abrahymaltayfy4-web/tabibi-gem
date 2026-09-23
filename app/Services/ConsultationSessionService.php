<?php

namespace App\Services;

use App\Domain\Communication\Contracts\CommunicationProviderInterface;
use App\Models\Consultation;
use App\Models\ConsultationEvent;
use App\Models\ConsultationSession;
use App\Models\User;
use App\Shared\Enums\SessionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsultationSessionService
{
    public function __construct(
        protected CommunicationProviderInterface $provider
    ) {}

    /**
     * Join consultation session with strict authorization & state transitions.
     *
     * @return array<string, mixed>
     */
    public function joinSession(User $user, int $consultationId): array
    {
        return DB::transaction(function () use ($user, $consultationId) {
            $consultation = Consultation::with(['appointment', 'patient', 'doctor'])
                ->lockForUpdate()
                ->findOrFail($consultationId);

            // Authorization Check: Must be the patient or doctor associated with consultation
            $isPatient = $user->patientProfile?->id === $consultation->patient_id;
            $isDoctor = $user->doctorProfile?->id === $consultation->doctor_id;

            if (! $isPatient && ! $isDoctor) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بالانضمام لهذه الجلسة الطبية.'],
                ]);
            }

            // State Machine Transition Rules
            $currentStatus = $consultation->session_status instanceof SessionStatus
                ? $consultation->session_status
                : (SessionStatus::tryFrom($consultation->session_status) ?? SessionStatus::SCHEDULED);

            if (in_array($currentStatus, [SessionStatus::COMPLETED, SessionStatus::TERMINATED, SessionStatus::CANCELLED, SessionStatus::FAILED])) {
                throw ValidationException::withMessages([
                    'session_status' => ['هذه الجلسة الطبية منتهية أو ملغاة ولا يمكن الانضمام إليها.'],
                ]);
            }

            // Advance status
            if ($currentStatus === SessionStatus::SCHEDULED || $currentStatus === SessionStatus::READY) {
                $consultation->session_status = SessionStatus::WAITING_FOR_PARTICIPANTS;
            } elseif ($currentStatus === SessionStatus::WAITING_FOR_PARTICIPANTS || $currentStatus === SessionStatus::RECONNECTING) {
                $consultation->session_status = SessionStatus::ACTIVE;
                if (! $consultation->started_at) {
                    $consultation->started_at = now();
                }
            }

            $consultation->save();

            // Record Session Log
            ConsultationSession::create([
                'consultation_id' => $consultation->id,
                'event_type' => 'UserJoined',
                'event_data_json' => [
                    'user_id' => $user->id,
                    'role' => $isDoctor ? 'doctor' : 'patient',
                    'session_status' => $consultation->session_status instanceof SessionStatus ? $consultation->session_status->value : $consultation->session_status,
                ],
                'recorded_by_user_id' => $user->id,
            ]);

            // Record Real-Time Consultation Event
            ConsultationEvent::create([
                'consultation_id' => $consultation->id,
                'actor_id' => $user->id,
                'event_name' => 'ParticipantJoined',
                'payload_json' => [
                    'role' => $isDoctor ? 'doctor' : 'patient',
                    'joined_at' => now()->toIso8601String(),
                ],
                'created_at' => now(),
            ]);

            // Generate RTC Token
            $roleStr = $isDoctor ? 'publisher' : 'subscriber';
            $rtcToken = $this->provider->generateToken(
                $consultation->agora_channel_name,
                $user->id,
                $roleStr,
                3600
            );

            $statusVal = $consultation->session_status instanceof SessionStatus ? $consultation->session_status->value : $consultation->session_status;

            return [
                'consultation_id' => $consultation->id,
                'channel_name' => $consultation->agora_channel_name,
                'rtc_token' => $rtcToken,
                'session_status' => $statusVal,
                'started_at' => $consultation->started_at?->toIso8601String(),
                'role' => $isDoctor ? 'doctor' : 'patient',
            ];
        });
    }

    /**
     * Handle participant leaving session temporarily or permanently.
     */
    public function leaveSession(User $user, int $consultationId): array
    {
        return DB::transaction(function () use ($user, $consultationId) {
            $consultation = Consultation::lockForUpdate()->findOrFail($consultationId);

            ConsultationSession::create([
                'consultation_id' => $consultation->id,
                'event_type' => 'UserLeft',
                'event_data_json' => ['user_id' => $user->id],
                'recorded_by_user_id' => $user->id,
            ]);

            ConsultationEvent::create([
                'consultation_id' => $consultation->id,
                'actor_id' => $user->id,
                'event_name' => 'ParticipantLeft',
                'payload_json' => ['left_at' => now()->toIso8601String()],
                'created_at' => now(),
            ]);

            $currentStatus = $consultation->session_status instanceof SessionStatus ? $consultation->session_status->value : $consultation->session_status;

            if ($currentStatus === SessionStatus::ACTIVE->value) {
                $consultation->session_status = SessionStatus::RECONNECTING;
                $consultation->save();
            }

            $finalStatus = $consultation->session_status instanceof SessionStatus ? $consultation->session_status->value : $consultation->session_status;

            return [
                'consultation_id' => $consultation->id,
                'session_status' => $finalStatus,
            ];
        });
    }

    /**
     * Terminate consultation session officially.
     */
    public function endSession(User $user, int $consultationId, ?string $reason = null): array
    {
        return DB::transaction(function () use ($user, $consultationId, $reason) {
            $consultation = Consultation::lockForUpdate()->findOrFail($consultationId);

            $isPatient = $user->patientProfile?->id === $consultation->patient_id;
            $isDoctor = $user->doctorProfile?->id === $consultation->doctor_id;

            $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('Super Admin');

            if (! $isPatient && ! $isDoctor && ! $isAdmin) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت غير مصرح لك بإنهاء هذه الجلسة الطبية.'],
                ]);
            }

            $consultation->session_status = SessionStatus::COMPLETED;
            $consultation->ended_at = now();
            $consultation->save();

            // Calculate duration in seconds
            $durationSeconds = $consultation->started_at
                ? $consultation->started_at->diffInSeconds($consultation->ended_at)
                : 0;

            ConsultationSession::create([
                'consultation_id' => $consultation->id,
                'event_type' => 'SessionEnded',
                'event_data_json' => [
                    'ended_by_user_id' => $user->id,
                    'reason' => $reason ?? 'Completed normally',
                    'duration_seconds' => $durationSeconds,
                ],
                'recorded_by_user_id' => $user->id,
            ]);

            ConsultationEvent::create([
                'consultation_id' => $consultation->id,
                'actor_id' => $user->id,
                'event_name' => 'CallEnded',
                'payload_json' => [
                    'reason' => $reason ?? 'Completed normally',
                    'duration_seconds' => $durationSeconds,
                ],
                'created_at' => now(),
            ]);

            $this->provider->closeChannel($consultation->agora_channel_name);

            $finalStatus = $consultation->session_status instanceof SessionStatus ? $consultation->session_status->value : $consultation->session_status;

            return [
                'consultation_id' => $consultation->id,
                'session_status' => $finalStatus,
                'started_at' => $consultation->started_at?->toIso8601String(),
                'ended_at' => $consultation->ended_at->toIso8601String(),
                'duration_seconds' => $durationSeconds,
            ];
        });
    }
}
