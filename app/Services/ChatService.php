<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Shared\Enums\MessageType;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChatService
{
    /**
     * Get or create conversation for consultation.
     */
    public function getOrCreateConversation(Consultation $consultation): Conversation
    {
        return DB::transaction(function () use ($consultation) {
            $conversation = Conversation::firstOrCreate(
                ['consultation_id' => $consultation->id],
                [
                    'title' => "استشارة طبية - موعد #{$consultation->appointment_id}",
                    'is_active' => true,
                ]
            );

            // Ensure participants attached
            $patientUser = User::whereHas('patientProfile', fn ($q) => $q->where('id', $consultation->patient_id))->first();
            $doctorUser = User::whereHas('doctorProfile', fn ($q) => $q->where('id', $consultation->doctor_id))->first();

            if ($patientUser) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $conversation->id,
                    'user_id' => $patientUser->id,
                ], [
                    'role_in_chat' => 'patient',
                ]);
            }

            if ($doctorUser) {
                ConversationParticipant::firstOrCreate([
                    'conversation_id' => $conversation->id,
                    'user_id' => $doctorUser->id,
                ], [
                    'role_in_chat' => 'doctor',
                ]);
            }

            return $conversation;
        });
    }

    /**
     * Send message with client idempotency key.
     */
    public function sendMessage(User $sender, int $conversationId, array $data): Message
    {
        return DB::transaction(function () use ($sender, $conversationId, $data) {
            $conversation = Conversation::findOrFail($conversationId);

            // Membership Check
            $isParticipant = ConversationParticipant::where('conversation_id', $conversationId)
                ->where('user_id', $sender->id)
                ->exists();

            if (! $isParticipant) {
                throw ValidationException::withMessages([
                    'authorization' => ['أنت لست طرفاً في هذه المحادثة الطبية.'],
                ]);
            }

            $clientMsgId = $data['client_msg_id'] ?? null;

            if ($clientMsgId) {
                $existing = Message::where('conversation_id', $conversationId)
                    ->where('client_msg_id', $clientMsgId)
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }

            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_id' => $sender->id,
                'client_msg_id' => $clientMsgId,
                'message_type' => $data['message_type'] ?? MessageType::TEXT->value,
                'content' => $data['content'] ?? null,
                'is_read' => false,
            ]);

            return $message;
        });
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(User $user, int $messageId): Message
    {
        $message = Message::findOrFail($messageId);

        $isParticipant = ConversationParticipant::where('conversation_id', $message->conversation_id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isParticipant) {
            throw ValidationException::withMessages([
                'authorization' => ['غير مصرح لك بتحديث حالة هذه الرسالة.'],
            ]);
        }

        if (! $message->is_read && $message->sender_id !== $user->id) {
            $message->is_read = true;
            $message->read_at = now();
            $message->save();
        }

        return $message;
    }

    /**
     * Get paginated messages (Cursor Pagination).
     */
    public function getMessages(User $user, int $conversationId, int $perPage = 20): CursorPaginator
    {
        $isParticipant = ConversationParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isParticipant) {
            throw ValidationException::withMessages([
                'authorization' => ['غير مصرح لك باسترجاع رسائل هذه المحادثة.'],
            ]);
        }

        return Message::with(['sender:id,full_name,email', 'attachments.file'])
            ->where('conversation_id', $conversationId)
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage);
    }
}
