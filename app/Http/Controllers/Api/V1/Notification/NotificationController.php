<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications($request->user());
        $unreadCount = $this->notificationService->getUnreadCount($request->user());

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
            'data' => $notifications,
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount($request->user());

        return response()->json([
            'status' => 'success',
            'unread_count' => $count,
        ]);
    }

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $success = $this->notificationService->markAsRead($request->user(), $id);

        if (! $success) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود أو لا ينتمي لهذا المستخدم.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعليم الإشعار كـ مقروء بنجاح.',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $updatedCount = $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'تم تعليم جميع الإشعارات كـ مقروءة بنجاح.',
            'updated_count' => $updatedCount,
        ]);
    }

    public function storePushToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_token' => 'required|string|max:500',
            'platform' => 'nullable|string|in:android,ios,web',
            'app_version' => 'nullable|string|max:50',
        ]);

        $token = $this->notificationService->storePushToken(
            $request->user(),
            $validated['device_token'],
            $validated['platform'] ?? 'android',
            $validated['app_version'] ?? null
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل توكن الجهاز وتفعيل التنبيهات المنبثقة بنجاح.',
            'data' => $token,
        ], 201);
    }

    public function deletePushToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_token' => 'required|string|max:500',
        ]);

        $this->notificationService->removePushToken(
            $request->user(),
            $validated['device_token']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'تم إلغاء توكن الجهاز بنجاح.',
        ]);
    }
}
