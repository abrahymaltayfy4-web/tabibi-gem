<?php

use App\Http\Controllers\Api\V1\Admin\AdminVerificationController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Doctor\DoctorDiscoveryController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;

Route::middleware([ForceJsonResponse::class])->prefix('v1')->group(function () {

    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('register/patient', [AuthController::class, 'registerPatient']);
        Route::post('register/doctor', [AuthController::class, 'registerDoctor']);
        Route::post('login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    // Public Doctor Discovery & Specialties
    Route::get('doctors', [DoctorDiscoveryController::class, 'index']);
    Route::get('doctors/{id}', [DoctorDiscoveryController::class, 'show']);
    Route::get('specialties', [DoctorDiscoveryController::class, 'specialties']);

    // Authenticated Patient & Doctor Endpoints
    Route::middleware('auth:sanctum')->group(function () {

        // Appointment Booking Engine
        Route::post('appointments/book', [AppointmentController::class, 'book']);
        Route::get('appointments', [AppointmentController::class, 'index']);
        Route::get('appointments/{id}', [AppointmentController::class, 'show']);

        // Payments (Sandboxed YER)
        Route::post('payments/checkout/{appointmentId}', [PaymentController::class, 'checkout']);
        Route::post('payments/sandbox-callback/{txRef}', [PaymentController::class, 'callback']);

        // Admin Control Panel Endpoints
        Route::prefix('admin')->group(function () {
            Route::get('verifications/pending', [AdminVerificationController::class, 'pendingDoctors']);
            Route::post('verifications/{doctorId}/approve', [AdminVerificationController::class, 'approve']);
            Route::post('verifications/{doctorId}/reject', [AdminVerificationController::class, 'reject']);
        });
    });

    // Health Check Endpoint
    Route::get('health', function () {
        return response()->json([
            'status' => 'healthy',
            'platform' => 'TABIBI Telemedicine Platform API',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });
});
