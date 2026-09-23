<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalAccessLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMedicalAuditController extends Controller
{
    public function logs(Request $request): JsonResponse
    {
        $logs = MedicalAccessLog::with(['actor', 'patient.user'])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
            ],
        ]);
    }
}
