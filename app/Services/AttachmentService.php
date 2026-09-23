<?php

namespace App\Services;

use App\Models\File;
use App\Models\FileAccessLog;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\User;
use App\Shared\Enums\MessageType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttachmentService
{
    /**
     * Upload medical attachment securely to private disk and link to conversation message.
     */
    public function uploadAttachment(User $uploader, int $conversationId, UploadedFile $file, string $category = 'chat'): Message
    {
        // Allowed Mimes Check
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (! in_array($file->getMimeType(), $allowedMimes)) {
            throw ValidationException::withMessages([
                'file' => ['نوع الملف غير مسموح به. يُسمح فقط بالصور والملفات الطبية بصيغة PDF و Word.'],
            ]);
        }

        // Size Limit 15MB
        if ($file->getSize() > 15 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'file' => ['حجم الملف يتجاوز الحد الأقصى المسموح به (15 ميجابايت).'],
            ]);
        }

        return DB::transaction(function () use ($uploader, $conversationId, $file, $category) {
            $uuid = (string) Str::uuid();
            $storedName = "{$uuid}.".$file->getClientOriginalExtension();
            $path = $file->storeAs("private/chat_attachments/{$conversationId}", $storedName, 'local');

            $checksum = hash_file('sha256', $file->getRealPath());

            $fileModel = File::create([
                'uuid' => $uuid,
                'uploader_id' => $uploader->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'storage_disk' => 'local',
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size_bytes' => $file->getSize(),
                'checksum_sha256' => $checksum,
                'category' => $category,
            ]);

            $isImage = str_starts_with($file->getMimeType(), 'image/');
            $messageType = $isImage ? MessageType::IMAGE->value : MessageType::FILE->value;

            $message = Message::create([
                'conversation_id' => $conversationId,
                'sender_id' => $uploader->id,
                'message_type' => $messageType,
                'content' => $file->getClientOriginalName(),
                'is_read' => false,
            ]);

            MessageAttachment::create([
                'message_id' => $message->id,
                'file_id' => $fileModel->id,
            ]);

            return $message;
        });
    }

    /**
     * Generate 15-minute temporary signed download URL with access auditing.
     */
    public function generateSignedUrl(User $actor, int $fileId, ?string $ipAddress = null): string
    {
        $file = File::findOrFail($fileId);

        // Access Audit Log
        FileAccessLog::create([
            'file_id' => $file->id,
            'actor_id' => $actor->id,
            'access_context' => 'chat_attachment_view',
            'ip_address' => $ipAddress,
            'accessed_at' => now(),
        ]);

        return URL::temporarySignedRoute(
            'api.v1.files.download',
            now()->addMinutes(15),
            ['fileId' => $file->id]
        );
    }
}
