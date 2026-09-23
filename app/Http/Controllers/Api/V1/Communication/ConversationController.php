<?php

namespace App\Http\Controllers\Api\V1\Communication;

use App\Http\Controllers\Controller;
use App\Services\AttachmentService;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(
        protected ChatService $chatService,
        protected AttachmentService $attachmentService
    ) {}

    public function messages(Request $request, int $id): JsonResponse
    {
        $messages = $this->chatService->getMessages($request->user(), $id);

        return response()->json([
            'status' => 'success',
            'data' => $messages->items(),
            'meta' => [
                'next_cursor' => $messages->nextCursor()?->encode(),
                'prev_cursor' => $messages->previousCursor()?->encode(),
                'has_more' => $messages->hasMorePages(),
            ],
        ]);
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required_without:client_msg_id|string|nullable|max:5000',
            'client_msg_id' => 'nullable|string|max:64',
            'message_type' => 'nullable|string|in:text,image,file,voice,system',
        ]);

        $message = $this->chatService->sendMessage($request->user(), $id, $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إرسال الرسالة بنجاح.',
            'data' => $message->load(['sender:id,full_name,email', 'attachments.file']),
        ], 201);
    }

    public function read(Request $request, int $messageId): JsonResponse
    {
        $message = $this->chatService->markAsRead($request->user(), $messageId);

        return response()->json([
            'status' => 'success',
            'message' => 'تم قراءة الرسالة.',
            'data' => [
                'message_id' => $message->id,
                'is_read' => $message->is_read,
                'read_at' => $message->read_at?->toIso8601String(),
            ],
        ]);
    }

    public function uploadAttachment(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:15360', // 15MB
            'category' => 'nullable|string|in:chat,medical,prescription',
        ]);

        $message = $this->attachmentService->uploadAttachment(
            $request->user(),
            $id,
            $request->file('file'),
            $request->input('category', 'chat')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم رفع وتأمين المستند الطبي بنجاح.',
            'data' => $message->load(['sender:id,name,email', 'attachments.file']),
        ], 201);
    }

    public function signedUrl(Request $request, int $fileId): JsonResponse
    {
        $url = $this->attachmentService->generateSignedUrl(
            $request->user(),
            $fileId,
            $request->ip()
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'file_id' => $fileId,
                'signed_url' => $url,
                'expires_in_minutes' => 15,
            ],
        ]);
    }
}
