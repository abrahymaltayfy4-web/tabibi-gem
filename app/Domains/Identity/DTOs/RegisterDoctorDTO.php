<?php

namespace App\Domains\Identity\DTOs;

readonly class RegisterDoctorDTO
{
    public function __construct(
        public string $fullName,
        public string $phone,
        public string $password,
        public int $primarySpecialtyId,
        public string $licenseNumber,
        public float $consultationPrice,
        public ?string $email = null,
        public ?string $bioAr = null,
        public int $yearsOfExperience = 0,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            fullName: $validatedData['full_name'],
            phone: $validatedData['phone'],
            password: $validatedData['password'],
            primarySpecialtyId: (int) $validatedData['primary_specialty_id'],
            licenseNumber: $validatedData['license_number'],
            consultationPrice: (float) ($validatedData['consultation_price'] ?? 5000.00),
            email: $validatedData['email'] ?? null,
            bioAr: $validatedData['bio_ar'] ?? null,
            yearsOfExperience: (int) ($validatedData['years_of_experience'] ?? 0),
        );
    }
}
