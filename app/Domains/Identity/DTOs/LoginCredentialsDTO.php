<?php

namespace App\Domains\Identity\DTOs;

readonly class LoginCredentialsDTO
{
    public function __construct(
        public string $phoneOrEmail,
        public string $password,
        public ?string $deviceToken = null,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            phoneOrEmail: $validatedData['phone_or_email'],
            password: $validatedData['password'],
            deviceToken: $validatedData['device_token'] ?? null,
        );
    }
}
