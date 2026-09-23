<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PresenceService
{
    /**
     * Record heartbeat pulse from client.
     */
    public function recordHeartbeat(User $user, string $status = 'Online'): void
    {
        $key = "user_presence:{$user->id}";
        Cache::put($key, [
            'status' => $status,
            'last_seen' => now()->toIso8601String(),
        ], 45); // 45s TTL
    }

    /**
     * Get user presence status.
     */
    public function getUserPresence(int $userId): array
    {
        $key = "user_presence:{$userId}";
        $presence = Cache::get($key);

        if (! $presence) {
            return [
                'user_id' => $userId,
                'status' => 'Offline',
                'last_seen' => null,
            ];
        }

        return array_merge(['user_id' => $userId], $presence);
    }
}
