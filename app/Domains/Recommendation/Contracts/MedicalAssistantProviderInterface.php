<?php

namespace App\Domains\Recommendation\Contracts;

interface MedicalAssistantProviderInterface
{
    /**
     * Analyze symptoms description and infer potential medical specialty and clarifying questions.
     */
    public function analyzeSymptoms(string $symptomsText): array;
}
