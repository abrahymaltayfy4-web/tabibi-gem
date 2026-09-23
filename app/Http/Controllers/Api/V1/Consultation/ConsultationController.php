<?php

namespace App\Http\Controllers\Api\V1\Consultation;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Services\ChatService;
use App\Services\ConsultationSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct(
        protected ConsultationSessionService $sessionService,
        protected ChatService $chatService
    ) {}

    public function join(Request $request, int $id): JsonResponse
    {
        $result = $this->sessionService->joinSession($request->user(), $id);

        $consultation = Consultation::findOrFail($id);
        $conversation = $this->chatService->getOrCreateConversation($consultation);

        $result['conversation_id'] = $conversation->id;

        return response()->json([
            'status' => 'success',
            'message' => 'تم الانضمام للجلسة الطبية بنجاح.',
            'data' => $result,
        ]);
    }

    public function leave(Request $request, int $id): JsonResponse
    {
        $result = $this->sessionService->leaveSession($request->user(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل مغادرة الجلسة مؤقتاً.',
            'data' => $result,
        ]);
    }

    public function end(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $result = $this->sessionService->endSession(
            $request->user(),
            $id,
            $request->input('reason')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنهاء الاستشارة الطبية رسمياً.',
            'data' => $result,
        ]);
    }
}
