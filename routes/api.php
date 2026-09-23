<?php

use App\Http\Controllers\Api\V1\Admin\AdminConsultationMonitoringController;
use App\Http\Controllers\Api\V1\Admin\AdminFinancialController;
use App\Http\Controllers\Api\V1\Admin\AdminMedicalAuditController;
use App\Http\Controllers\Api\V1\Admin\AdminRecommendationRulesController;
use App\Http\Controllers\Api\V1\Admin\AdminVerificationController;
use App\Http\Controllers\Api\V1\Appointment\AppointmentController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Communication\ConversationController;
use App\Http\Controllers\Api\V1\Communication\PresenceController;
use App\Http\Controllers\Api\V1\Consultation\ConsultationController;
use App\Http\Controllers\Api\V1\Doctor\DoctorDiscoveryController;
use App\Http\Controllers\Api\V1\Doctor\DoctorFinancialController;
use App\Http\Controllers\Api\V1\Ehr\ClinicalDataController;
use App\Http\Controllers\Api\V1\Ehr\MedicalRecordController;
use App\Http\Controllers\Api\V1\Ehr\PatientMedicalHistoryController;
use App\Http\Controllers\Api\V1\Ehr\PrescriptionController;
use App\Http\Controllers\Api\V1\Notification\NotificationController;
use App\Http\Controllers\Api\V1\Payment\PaymentController;
use App\Http\Controllers\Api\V1\Recommendation\SmartMedicalAssistantController;
use App\Http\Middleware\EnsureAdmin;
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

        // Smart Medical Assistant & Recommendation Engine
        Route::post('medical-assistant/analyze', [SmartMedicalAssistantController::class, 'analyze']);
        Route::post('recommendations/{id}/feedback', [SmartMedicalAssistantController::class, 'feedback']);

        // Appointment Booking Engine
        Route::post('appointments/book', [AppointmentController::class, 'book']);
        Route::get('appointments', [AppointmentController::class, 'index']);
        Route::get('appointments/{id}', [AppointmentController::class, 'show']);

        // Payments & Financial Transactions (Sandboxed YER & Manual Proofs)
        Route::post('payments/checkout/{appointmentId}', [PaymentController::class, 'checkout']);
        Route::post('payments/sandbox-callback/{txRef}', [PaymentController::class, 'callback']);
        Route::post('payments/manual-proof/{appointmentId}', [PaymentController::class, 'uploadManualProof']);
        Route::get('invoices/{invoiceId}', [PaymentController::class, 'invoice']);

        // Doctor Financial Earnings & Payout Requests
        Route::get('doctor/earnings', [DoctorFinancialController::class, 'earnings']);
        Route::post('doctor/payouts/request', [DoctorFinancialController::class, 'requestPayout']);

        // Real-Time Consultations & Communication Engine
        Route::prefix('consultations')->group(function () {
            Route::post('{id}/join', [ConsultationController::class, 'join']);
            Route::post('{id}/leave', [ConsultationController::class, 'leave']);
            Route::post('{id}/end', [ConsultationController::class, 'end']);
            Route::post('{id}/medical-records', [MedicalRecordController::class, 'store']);
            Route::post('{id}/prescriptions', [PrescriptionController::class, 'store']);
            Route::post('{id}/clinical-notes', [ClinicalDataController::class, 'storeClinicalNotes']);
        });

        // EHR & Medical Records Data Access
        Route::put('medical-records/{id}', [MedicalRecordController::class, 'amend']);
        Route::get('patients/{patientId}/medical-profile', [ClinicalDataController::class, 'profile']);
        Route::get('patients/{patientId}/medical-history', [MedicalRecordController::class, 'history']);
        Route::get('patients/{patientId}/longitudinal-history', [PatientMedicalHistoryController::class, 'index']);
        Route::post('patients/{patientId}/longitudinal-history', [PatientMedicalHistoryController::class, 'store']);
        Route::post('patients/{patientId}/medical-documents', [ClinicalDataController::class, 'uploadDocument']);

        // Electronic Prescriptions Actions & Lifecycle
        Route::post('prescriptions/{id}/finalize', [PrescriptionController::class, 'finalize']);
        Route::post('prescriptions/{id}/amend', [PrescriptionController::class, 'amend']);
        Route::get('patients/{patientId}/prescriptions', [PrescriptionController::class, 'patientPrescriptions']);

        Route::prefix('conversations')->group(function () {
            Route::get('{id}/messages', [ConversationController::class, 'messages']);
            Route::post('{id}/messages', [ConversationController::class, 'sendMessage']);
            Route::post('{id}/attachments', [ConversationController::class, 'uploadAttachment']);
        });

        Route::post('messages/{id}/read', [ConversationController::class, 'read']);
        Route::get('files/{fileId}/signed-url', [ConversationController::class, 'signedUrl'])->name('api.v1.files.download');

        // Presence & Heartbeat
        Route::post('presence/heartbeat', [PresenceController::class, 'heartbeat']);
        Route::get('presence/{userId}', [PresenceController::class, 'show']);

        // Notification & Real-Time Events Engine
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Devices Push Token Registration & Deactivation
        Route::post('devices/push-token', [NotificationController::class, 'storePushToken']);
        Route::delete('devices/push-token', [NotificationController::class, 'deletePushToken']);

        // Admin Control Panel Endpoints (Protected by EnsureAdmin middleware)
        Route::prefix('admin')->middleware([EnsureAdmin::class])->group(function () {
            Route::get('verifications/pending', [AdminVerificationController::class, 'pendingDoctors']);
            Route::post('verifications/{doctorId}/approve', [AdminVerificationController::class, 'approve']);
            Route::post('verifications/{doctorId}/reject', [AdminVerificationController::class, 'reject']);

            // Admin Financial Oversight & Verification
            Route::get('payments', [AdminFinancialController::class, 'payments']);
            Route::post('payments/{paymentId}/verify-manual', [AdminFinancialController::class, 'verifyManual']);
            Route::post('payments/{paymentId}/refund', [AdminFinancialController::class, 'refund']);
            Route::get('payouts', [AdminFinancialController::class, 'payouts']);
            Route::post('payouts/{payoutId}/approve', [AdminFinancialController::class, 'approvePayout']);

            // Admin Medical Audit Logs
            Route::get('medical-access-logs', [AdminMedicalAuditController::class, 'logs']);

            // Admin Recommendation Engine & Safety Logs
            Route::get('recommendations/rules', [AdminRecommendationRulesController::class, 'rules']);
            Route::post('recommendations/rules', [AdminRecommendationRulesController::class, 'storeRule']);
            Route::get('recommendations/safety-logs', [AdminRecommendationRulesController::class, 'safetyLogs']);

            // Admin Consultation Live Monitoring & Events
            Route::get('consultations/active', [AdminConsultationMonitoringController::class, 'activeSessions']);
            Route::get('consultations/{id}/events', [AdminConsultationMonitoringController::class, 'events']);
            Route::get('consultations/reports', [AdminConsultationMonitoringController::class, 'reports']);
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
