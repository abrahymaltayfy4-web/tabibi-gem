<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationReport;
use App\Models\Consultation;
use App\Models\ConsultationEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminConsultationMonitoringController extends Controller
{
    public function activeSessions(Request $request): JsonResponse
    {
        $activeConsultations = Consultation::with(['patient.user', 'doctor.user', 'appointment'])
            ->whereIn('session_status', ['WaitingForParticipants', 'Active', 'Reconnecting'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $activeConsultations,
        ]);
    }

    public function events(Request $request, int $consultationId): JsonResponse
    {
        $events = ConsultationEvent::with('actor')
            ->where('consultation_id', $consultationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $events,
        ]);
    }

    public function reports(Request $request): JsonResponse
    {
        $reports = CommunicationReport::with(['reporter', 'consultation', 'resolvedByAdmin'])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return response()->json([
            'status' => 'success',
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'total' => $reports->total(),
            ],
        ]);
    }
}
