<?php

namespace App\Domains\Identity\Actions;

use App\Domains\Identity\DTOs\LoginCredentialsDTO;
use App\Models\User;
use App\Shared\Enums\AccountStatus;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthenticateUserAction
{
    public function execute(LoginCredentialsDTO $dto): array
    {
        $user = User::where('phone', $dto->phoneOrEmail)
            ->orWhere('email', $dto->phoneOrEmail)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password_hash)) {
            throw ValidationException::withMessages([
                'phone_or_email' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        if ($user->account_status === AccountStatus::SUSPENDED || $user->account_status === AccountStatus::BLOCKED) {
            throw ValidationException::withMessages([
                'phone_or_email' => ['الحساب معطل أو مجمع مؤقتاً. يرجى التواصل مع الدعم الفني.'],
            ]);
        }

        $user->update([
            'last_login_at' => now(),
            'last_activity_at' => now(),
        ]);

        $tokenName = 'auth_token_' . strtolower($user->roles()->first()?->name ?? 'user');
        $token = $user->createToken($tokenName)->plainTextToken;

        return [
            'user' => $user->load(['patientProfile', 'doctorProfile.primarySpecialty', 'roles']),
            'token' => $token,
        ];
    }
}
