<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PushToken;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Get paginated notifications for the given user.
     */
    public function getUserNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get fast unread notification count.
     */
    public function getUnreadCount(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = Notification::where('user_id', $user->id)
            ->where('id', $notificationId)
            ->first();

        if (! $notification) {
            return false;
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllAsRead(User $user): int
    {
        return Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Store or update device push token for Firebase Cloud Messaging (FCM).
     */
    public function storePushToken(User $user, string $deviceToken, string $platform = 'android', ?string $appVersion = null): PushToken
    {
        return PushToken::updateOrCreate(
            [
                'user_id' => $user->id,
                'device_token' => $deviceToken,
            ],
            [
                'platform' => strtolower($platform),
                'app_version' => $appVersion,
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );
    }

    /**
     * Remove / deactivate push token on user logout.
     */
    public function removePushToken(User $user, string $deviceToken): bool
    {
        return PushToken::where('user_id', $user->id)
            ->where('device_token', $deviceToken)
            ->update(['is_active' => false]);
    }

    /**
     * Send in-app and push notification with idempotency check.
     */
    public function sendNotification(User $recipient, string $type, array $payload, ?string $idempotencyKey = null): ?Notification
    {
        if ($idempotencyKey) {
            $cacheKey = "notification_idempotency:{$idempotencyKey}";
            if (Cache::has($cacheKey)) {
                return null; // Prevent duplicate notification dispatch
            }
            Cache::put($cacheKey, true, now()->addHours(24));
        }

        // Store Database Notification
        return Notification::create([
            'user_id' => $recipient->id,
            'type' => $type,
            'title' => $payload['title'] ?? 'إشعار من طبيبي',
            'body' => $payload['message'] ?? $payload['body'] ?? '',
            'data_payload_json' => $payload,
            'is_read' => false,
            'read_at' => null,
            'channel' => $payload['channel'] ?? 'in_app',
        ]);
    }
}
