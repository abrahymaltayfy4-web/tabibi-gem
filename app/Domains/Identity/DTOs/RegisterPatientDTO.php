<?php

namespace App\Domains\Identity\DTOs;

readonly class RegisterPatientDTO
{
    public function __construct(
        public string $fullName,
        public string $phone,
        public string $password,
        public ?string $email = null,
        public ?string $dateOfBirth = null,
        public string $gender = 'Male',
        public ?string $bloodGroup = null,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            fullName: $validatedData['full_name'],
            phone: $validatedData['phone'],
            password: $validatedData['password'],
            email: $validatedData['email'] ?? null,
            dateOfBirth: $validatedData['date_of_birth'] ?? null,
            gender: $validatedData['gender'] ?? 'Male',
            bloodGroup: $validatedData['blood_group'] ?? null,
        );
    }
}
