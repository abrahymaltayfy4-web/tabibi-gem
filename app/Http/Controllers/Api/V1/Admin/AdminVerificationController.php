<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DoctorProfileResource;
use App\Models\DoctorProfile;
use App\Models\DoctorVerificationRequest;
use App\Shared\Enums\VerificationStatus;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminVerificationController extends Controller
{
    use ApiResponse;

    public function pendingDoctors(): JsonResponse
    {
        $pendingDoctors = DoctorProfile::with(['user', 'primarySpecialty', 'verificationRequests'])
            ->where('verification_status', VerificationStatus::PENDING)
            ->paginate(15);

        return $this->successResponse(
            data: DoctorProfileResource::collection($pendingDoctors->items()),
            message: 'تم إرجاع قائمة طلبات توثيق الأطباء المعلقة',
            meta: [
                'current_page' => $pendingDoctors->currentPage(),
                'total' => $pendingDoctors->total(),
            ]
        );
    }

    public function approve(int $doctorId, Request $request): JsonResponse
    {
        $doctor = DoctorProfile::findOrFail($doctorId);
        $doctor->update([
            'verification_status' => VerificationStatus::APPROVED,
        ]);

        DoctorVerificationRequest::create([
            'doctor_id' => $doctor->id,
            'status' => VerificationStatus::APPROVED->value,
            'reviewed_by_admin_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return $this->successResponse(
            data: new DoctorProfileResource($doctor->fresh(['user', 'primarySpecialty'])),
            message: 'تم اعتماد وتوثيق ترخيص الطبيب بنجاح'
        );
    }

    public function reject(int $doctorId, Request $request): JsonResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $doctor = DoctorProfile::findOrFail($doctorId);
        $doctor->update([
            'verification_status' => VerificationStatus::REJECTED,
        ]);

        DoctorVerificationRequest::create([
            'doctor_id' => $doctor->id,
            'status' => VerificationStatus::REJECTED->value,
            'rejection_reason' => $request->input('rejection_reason'),
            'reviewed_by_admin_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return $this->successResponse(
            data: new DoctorProfileResource($doctor->fresh(['user', 'primarySpecialty'])),
            message: 'تم رفض طلب توثيق الطبيب مع تسجيل الأسباب'
        );
    }
}
