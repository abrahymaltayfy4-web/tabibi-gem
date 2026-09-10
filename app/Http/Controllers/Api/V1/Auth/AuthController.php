<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domains\Identity\Actions\AuthenticateUserAction;
use App\Domains\Identity\Actions\RegisterDoctorAction;
use App\Domains\Identity\Actions\RegisterPatientAction;
use App\Domains\Identity\DTOs\LoginCredentialsDTO;
use App\Domains\Identity\DTOs\RegisterDoctorDTO;
use App\Domains\Identity\DTOs\RegisterPatientDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterDoctorRequest;
use App\Http\Requests\Api\V1\Auth\RegisterPatientRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function registerPatient(
        RegisterPatientRequest $request,
        RegisterPatientAction $action
    ): JsonResponse {
        $dto = RegisterPatientDTO::fromRequest($request->validated());
        $result = $action->execute($dto);

        return $this->successResponse(
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            message: 'تم إنشاء حساب المريض بنجاح',
            statusCode: Response::HTTP_CREATED
        );
    }

    public function registerDoctor(
        RegisterDoctorRequest $request,
        RegisterDoctorAction $action
    ): JsonResponse {
        $dto = RegisterDoctorDTO::fromRequest($request->validated());
        $result = $action->execute($dto);

        return $this->successResponse(
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            message: 'تم تسجيل حساب الطبيب بنجاح وهو قيد التوثيق',
            statusCode: Response::HTTP_CREATED
        );
    }

    public function login(
        LoginRequest $request,
        AuthenticateUserAction $action
    ): JsonResponse {
        $dto = LoginCredentialsDTO::fromRequest($request->validated());
        $result = $action->execute($dto);

        return $this->successResponse(
            data: [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
            message: 'تم تسجيل الدخول بنجاح'
        );
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['patientProfile', 'doctorProfile.primarySpecialty', 'roles']);

        return $this->successResponse(
            data: new UserResource($user),
            message: 'تم جلب بيانات المستخدم الحالية'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            data: [],
            message: 'تم تسجيل الخروج بنجاح'
        );
    }
}
