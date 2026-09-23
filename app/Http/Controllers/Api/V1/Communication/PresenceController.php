<?php

namespace App\Http\Controllers\Api\V1\Communication;

use App\Http\Controllers\Controller;
use App\Services\PresenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function __construct(
        protected PresenceService $presenceService
    ) {}

    public function heartbeat(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'nullable|string|in:Online,InConsultation,Connecting,Busy',
        ]);

        $this->presenceService->recordHeartbeat(
            $request->user(),
            $request->input('status', 'Online')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Heartbeat acknowledged',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function show(int $userId): JsonResponse
    {
        $presence = $this->presenceService->getUserPresence($userId);

        return response()->json([
            'status' => 'success',
            'data' => $presence,
        ]);
    }
}
