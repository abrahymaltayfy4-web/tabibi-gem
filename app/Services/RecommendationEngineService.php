<?php

namespace App\Services;

use App\Domains\Recommendation\Contracts\MedicalAssistantProviderInterface;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\RecommendationRequest;
use App\Models\RecommendationResult;
use App\Models\Specialty;
use App\Models\User;

class RecommendationEngineService
{
    public function __construct(
        protected EmergencyDetectionService $emergencyService,
        protected MedicalAssistantProviderInterface $assistantProvider
    ) {}

    /**
     * Process symptom input, perform safety checks, infer specialty, and score matching doctors.
     */
    public function getRecommendations(User $actor, string $symptomsText, array $filters = []): array
    {
        // 1. Emergency Detection & Red Flag Screening
        $emergencyCheck = $this->emergencyService->detectEmergency($symptomsText, $actor);
        if ($emergencyCheck && $emergencyCheck['is_emergency']) {
            return [
                'status' => 'emergency_alert',
                'emergency_warning' => $emergencyCheck,
                'recommended_doctors' => [],
            ];
        }

        // 2. Prompt Security & Input Sanitization
        $cleanText = $this->sanitizeInput($symptomsText);

        // 3. Analyze Symptoms via Provider Abstraction (Rule-Based or LLM Adapter)
        $analysis = $this->assistantProvider->analyzeSymptoms($cleanText);
        $specialtyCode = $filters['specialty_code'] ?? $analysis['suggested_specialty_code'];

        $specialty = Specialty::where('code', $specialtyCode)->first();

        // 4. Record Recommendation Request
        $patientId = $actor->patientProfile?->id ?? PatientProfile::firstOrCreate(['user_id' => $actor->id], ['first_name' => 'زائر', 'last_name' => 'طبيبي', 'gender' => 'Male'])->id;

        $recRequest = RecommendationRequest::create([
            'patient_id' => $patientId,
            'symptoms_description' => $cleanText,
            'category_code' => $specialtyCode,
            'max_price_yer' => $filters['max_price_yer'] ?? null,
            'preferred_gender' => $filters['preferred_gender'] ?? null,
        ]);

        // 5. Fetch Candidate Doctors & Compute Weighted Scores
        $doctorQuery = DoctorProfile::with(['user', 'primarySpecialty'])
            ->where('verification_status', 'Approved');

        if ($specialty) {
            $doctorQuery->where('primary_specialty_id', $specialty->id);
        }

        if (! empty($filters['max_price_yer'])) {
            $doctorQuery->where('consultation_price', '<=', $filters['max_price_yer']);
        }

        $candidateDoctors = $doctorQuery->get();

        $scoredDoctors = [];
        $rankPosition = 1;

        foreach ($candidateDoctors as $doctor) {
            $scoreDetails = $this->calculateDoctorScore($doctor, $specialtyCode, $filters);
            $totalScore = $scoreDetails['total_score'];

            // Store result in DB
            RecommendationResult::create([
                'request_id' => $recRequest->id,
                'doctor_id' => $doctor->id,
                'match_score' => $totalScore,
                'match_reasons_json' => $scoreDetails['explanations'],
                'rank_position' => $rankPosition++,
            ]);

            $scoredDoctors[] = [
                'doctor_id' => $doctor->id,
                'doctor_name' => $doctor->user->full_name ?? 'دكتور طبيبي',
                'specialty_name_ar' => $doctor->primarySpecialty?->name_ar ?? 'عام',
                'license_number' => $doctor->license_number,
                'consultation_price' => $doctor->consultation_price,
                'match_score' => $totalScore,
                'explanations' => $scoreDetails['explanations'],
            ];
        }

        // Sort by match score descending
        usort($scoredDoctors, fn ($a, $b) => $b['match_score'] <=> $a['match_score']);

        return [
            'status' => 'success',
            'request_id' => $recRequest->id,
            'suggested_specialty' => [
                'code' => $specialtyCode,
                'name_ar' => $specialty?->name_ar ?? 'الطب العام',
            ],
            'matched_keyword' => $analysis['matched_keyword'],
            'clarifying_questions' => $analysis['clarifying_questions'],
            'disclaimer' => $analysis['disclaimer'],
            'recommended_doctors' => $scoredDoctors,
        ];
    }

    /**
     * Calculate transparent weighted score for a candidate doctor.
     */
    protected function calculateDoctorScore(DoctorProfile $doctor, string $targetSpecialtyCode, array $filters): array
    {
        $score = 0.0;
        $explanations = [];

        // 1. Specialty Match (40 Points)
        if ($doctor->primarySpecialty?->code === $targetSpecialtyCode) {
            $score += 40.0;
            $explanations[] = 'التخصص ينطبق تماماً على الأعراض الموصوفة';
        } else {
            $score += 20.0;
        }

        // 2. Active Clinic & Availability (25 Points)
        if ($doctor->is_active_clinic ?? true) {
            $score += 25.0;
            $explanations[] = 'عيادة الطبيب نشطة ومتاحة لاستقبال المواعيد الأونلاين';
        }

        // 3. Verification & License (15 Points)
        if ($doctor->verification_status === 'Approved') {
            $score += 15.0;
            $explanations[] = 'طبيب معتمد وموثق رخصته من المنصة';
        }

        // 4. Consultation Price Fit (10 Points)
        if (! empty($filters['max_price_yer']) && $doctor->consultation_price <= $filters['max_price_yer']) {
            $score += 10.0;
            $explanations[] = 'سعر الاستشارة يطابق الميزانية المحددة';
        } else {
            $score += 10.0;
        }

        // 5. Platform Quality Baseline (10 Points)
        $score += 10.0;

        return [
            'total_score' => round($score, 2),
            'explanations' => $explanations,
        ];
    }

    /**
     * Sanitize input against prompt injection attempts.
     */
    protected function sanitizeInput(string $input): string
    {
        // Strip out dangerous patterns or prompt injection system overrides
        $forbiddenPatterns = [
            '/ignore previous instructions/i',
            '/override safety/i',
            '/you are now a doctor/i',
            '/act as a physician/i',
        ];

        return preg_replace($forbiddenPatterns, '', trim($input));
    }
}
