<?php

namespace App\Shared\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    protected function successResponse(
        mixed $data = [],
        string $message = 'تمت العملية بنجاح',
        int $statusCode = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    protected function errorResponse(
        string $message = 'حدث خطأ في المعالجة',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $errors = [],
        string $errorCode = 'BAD_REQUEST'
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'code' => $errorCode,
        ];

        return response()->json($response, $statusCode);
    }

    protected function unauthorizedResponse(string $message = 'غير مسموح بالوصول'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED, [], 'UNAUTHORIZED');
    }

    protected function forbiddenResponse(string $message = 'صلاحية غير كافية'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_FORBIDDEN, [], 'FORBIDDEN');
    }

    protected function notFoundResponse(string $message = 'المورد المطلوب غير موجود'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND, [], 'NOT_FOUND');
    }
}
