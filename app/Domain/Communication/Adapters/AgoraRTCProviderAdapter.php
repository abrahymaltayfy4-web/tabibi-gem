<?php

namespace App\Domain\Communication\Adapters;

use App\Domain\Communication\Contracts\CommunicationProviderInterface;

class AgoraRTCProviderAdapter implements CommunicationProviderInterface
{
    protected string $appId;

    protected string $appCertificate;

    public function __construct()
    {
        $this->appId = config('services.agora.app_id', 'tabibi_agora_app_id_sandbox');
        $this->appCertificate = config('services.agora.app_certificate', 'tabibi_agora_cert_sandbox');
    }

    public function createChannel(string $channelName): array
    {
        return [
            'channel_name' => $channelName,
            'status' => 'created',
            'provider' => 'AgoraRTC',
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function generateToken(string $channelName, int $userId, string $role, int $expireSeconds = 3600): string
    {
        // Sandbox / Production Agora Token calculation algorithm representation
        $timestamp = time() + $expireSeconds;
        $signature = hash_hmac('sha256', "{$channelName}:{$userId}:{$role}:{$timestamp}", $this->appCertificate);

        return "AGORA_RTC_TOKEN_v1:{$this->appId}:{$channelName}:{$userId}:{$timestamp}:{$signature}";
    }

    public function closeChannel(string $channelName): bool
    {
        return true;
    }

    public function getChannelStats(string $channelName): array
    {
        return [
            'channel_name' => $channelName,
            'active_participants' => 2,
            'media_quality' => 'good',
            'bitrate_kbps' => 450,
        ];
    }
}
