<?php

namespace App\Http\Controllers\Api\V1\Appointment;

use App\Domains\Booking\Actions\CreateAppointmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Appointment\CreateAppointmentRequest;
use App\Http\Resources\Api\V1\AppointmentResource;
use App\Models\Appointment;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function book(
        CreateAppointmentRequest $request,
        CreateAppointmentAction $action
    ): JsonResponse {
        $patient = $request->user()->patientProfile;
        if (! $patient) {
            return $this->errorResponse('حسابك الحالي ليس حساب مريض.', Response::HTTP_FORBIDDEN);
        }

        $appointment = $action->execute($patient, $request->validated());

        return $this->successResponse(
            data: new AppointmentResource($appointment),
            message: 'تم حجز الموعد بنجاح وهو في انتظار إتمام عملية الدفع بالريال اليمني',
            statusCode: Response::HTTP_CREATED
        );
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Appointment::with(['patient.user', 'doctor.user', 'doctor.primarySpecialty', 'appointmentType']);

        if ($user->patientProfile) {
            $query->where('patient_id', $user->patientProfile->id);
        } elseif ($user->doctorProfile) {
            $query->where('doctor_id', $user->doctorProfile->id);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return $this->successResponse(
            data: AppointmentResource::collection($appointments->items()),
            message: 'تم جلب قائمة المواعيد بنجاح',
            meta: [
                'current_page' => $appointments->currentPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ]
        );
    }

    public function show(int $id, Request $request): JsonResponse
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user', 'doctor.primarySpecialty', 'appointmentType'])
            ->findOrFail($id);

        $user = $request->user();
        $isPatientOwner = $user->patientProfile && $user->patientProfile->id === $appointment->patient_id;
        $isDoctorOwner = $user->doctorProfile && $user->doctorProfile->id === $appointment->doctor_id;
        $isAdmin = $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();

        if (! $isPatientOwner && ! $isDoctorOwner && ! $isAdmin) {
            return $this->forbiddenResponse('ليس لديك صلاحية الاطلاع على تفاصيل هذا الموعد.');
        }

        return $this->successResponse(
            data: new AppointmentResource($appointment),
            message: 'تم إرجاع تفاصيل الموعد'
        );
    }
}
