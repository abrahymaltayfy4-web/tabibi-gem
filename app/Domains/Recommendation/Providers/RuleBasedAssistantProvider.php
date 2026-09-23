<?php

namespace App\Domains\Recommendation\Providers;

use App\Domains\Recommendation\Contracts\MedicalAssistantProviderInterface;
use App\Models\RecommendationRule;

class RuleBasedAssistantProvider implements MedicalAssistantProviderInterface
{
    protected array $defaultKeywordMap = [
        'جلد' => 'DERM',
        'حب الشباب' => 'DERM',
        'طفح' => 'DERM',
        'حكة' => 'DERM',
        'skin' => 'DERM',
        'itch' => 'DERM',
        'dermatology' => 'DERM',
        'قلب' => 'CARDIO',
        'خفقان' => 'CARDIO',
        'نبض' => 'CARDIO',
        'heart' => 'CARDIO',
        'رأس' => 'NEURO',
        'صداع' => 'NEURO',
        'دوخة' => 'NEURO',
        'أعصاب' => 'NEURO',
        'headache' => 'NEURO',
        'عظام' => 'ORTHO',
        'مفاصل' => 'ORTHO',
        'ظهر' => 'ORTHO',
        'ركبة' => 'ORTHO',
        'bone' => 'ORTHO',
        'طفل' => 'PEDIATRICS',
        'أطفال' => 'PEDIATRICS',
        'رضيع' => 'PEDIATRICS',
        'child' => 'PEDIATRICS',
        'أذن' => 'ENT',
        'أنف' => 'ENT',
        'حلق' => 'ENT',
        'عين' => 'EYE',
        'رؤية' => 'EYE',
        'معدة' => 'INT_MED',
        'بطن' => 'INT_MED',
        'غثيان' => 'INT_MED',
        'حرارة' => 'INT_MED',
        'حمى' => 'INT_MED',
        'fever' => 'INT_MED',
        'stomach' => 'INT_MED',
        'قلق' => 'PSYCH',
        'نوم' => 'PSYCH',
        'اكتئاب' => 'PSYCH',
    ];

    public function analyzeSymptoms(string $symptomsText): array
    {
        $normalizedText = mb_strtolower($symptomsText);
        $suggestedSpecialty = 'INT_MED'; // Default general internal medicine
        $matchedKeyword = null;

        // 1. First check DB active rules
        $activeRules = RecommendationRule::where('is_active', true)->get();
        foreach ($activeRules as $rule) {
            if (mb_strpos($normalizedText, mb_strtolower($rule->symptom_keyword)) !== false) {
                $suggestedSpecialty = $rule->specialty_code;
                $matchedKeyword = $rule->symptom_keyword;
                break;
            }
        }

        // 2. Fallback to default keyword map if no DB rule matched
        if (! $matchedKeyword) {
            foreach ($this->defaultKeywordMap as $keyword => $code) {
                if (mb_strpos($normalizedText, mb_strtolower($keyword)) !== false) {
                    $suggestedSpecialty = $code;
                    $matchedKeyword = $keyword;
                    break;
                }
            }
        }

        return [
            'suggested_specialty_code' => $suggestedSpecialty,
            'matched_keyword' => $matchedKeyword,
            'clarifying_questions' => [
                'ما هي مدة ظهور هذه الأعراض؟',
                'هل تزداد الأعراض حدة في أوقات معينة من اليوم؟',
                'هل توجد أي أعراض أخرى مصاحبة؟',
            ],
            'disclaimer' => 'المساعد الذكي يوفر توصيات إرشادية لاقتراح التخصص والأطباء الأنسب، ولا يقدم تشخيصاً طبياً نائباً عن الطبيب.',
        ];
    }
}
