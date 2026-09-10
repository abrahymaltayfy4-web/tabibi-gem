<?php

namespace App\Domains\Doctor\Actions;

use App\Models\DoctorProfile;
use App\Shared\Enums\VerificationStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SearchDoctorsAction
{
    public function execute(Request $request): LengthAwarePaginator
    {
        $query = DoctorProfile::with(['user', 'primarySpecialty', 'subSpecialties'])
            ->where('verification_status', VerificationStatus::APPROVED)
            ->where('is_active_clinic', true);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialty_id')) {
            $query->where('primary_specialty_id', $request->input('specialty_id'));
        }

        if ($request->filled('max_price')) {
            $query->where('consultation_price', '<=', $request->input('max_price'));
        }

        if ($request->filled('min_rating')) {
            $query->where('rating_avg', '>=', $request->input('min_rating'));
        }

        $sortBy = $request->input('sort_by', 'rating_avg');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int) $request->input('per_page', 15);

        return $query->paginate($perPage);
    }
}
