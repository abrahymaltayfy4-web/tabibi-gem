<?php

namespace App\Http\Controllers\Api\V1\Doctor;

use App\Domains\Doctor\Actions\SearchDoctorsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DoctorProfileResource;
use App\Models\DoctorProfile;
use App\Models\Specialty;
use App\Shared\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorDiscoveryController extends Controller
{
    use ApiResponse;

    public function index(Request $request, SearchDoctorsAction $action): JsonResponse
    {
        $paginatedDoctors = $action->execute($request);

        return $this->successResponse(
            data: DoctorProfileResource::collection($paginatedDoctors->items()),
            message: 'تم إرجاع قائمة الأطباء المعتمدين بنجاح',
            meta: [
                'current_page' => $paginatedDoctors->currentPage(),
                'per_page' => $paginatedDoctors->perPage(),
                'total' => $paginatedDoctors->total(),
                'last_page' => $paginatedDoctors->lastPage(),
            ]
        );
    }

    public function show(int $id): JsonResponse
    {
        $doctor = DoctorProfile::with(['user', 'primarySpecialty', 'subSpecialties', 'availabilityRules'])
            ->findOrFail($id);

        return $this->successResponse(
            data: new DoctorProfileResource($doctor),
            message: 'تم إرجاع التفاصيل المهنية للطبيب'
        );
    }

    public function specialties(): JsonResponse
    {
        $specialties = Specialty::where('is_active', true)->get();

        return $this->successResponse(
            data: $specialties,
            message: 'تم جلب دليل التخصصات الطبية'
        );
    }
}
