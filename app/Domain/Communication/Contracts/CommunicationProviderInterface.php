<?php

namespace App\Domain\Communication\Contracts;

interface CommunicationProviderInterface
{
    /**
     * Create a real-time communication channel.
     *
     * @return array{channel_name: string, status: string}
     */
    public function createChannel(string $channelName): array;

    /**
     * Generate access token for participant joining the channel.
     */
    public function generateToken(string $channelName, int $userId, string $role, int $expireSeconds = 3600): string;

    /**
     * Close real-time communication channel.
     */
    public function closeChannel(string $channelName): bool;

    /**
     * Get real-time stats for the channel.
     *
     * @return array<string, mixed>
     */
    public function getChannelStats(string $channelName): array;
}
