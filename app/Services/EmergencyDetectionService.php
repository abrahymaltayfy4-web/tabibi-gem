<?php

namespace App\Services;

use App\Models\SafetyEventLog;
use App\Models\User;

class EmergencyDetectionService
{
    protected array $emergencyKeywords = [
        'ألم شديد في الصدر',
        'ألم الصدر',
        'ضغط على الصدر',
        'نوبة قلبية',
        'ضيق تنفس حاد',
        'صعوبة شديدة في التنفس',
        'فقدان الوعي',
        'إغماء مفاجئ',
        'نزيف حاد',
        'شلل مفاجئ',
        'ثقل في اللسان',
        'تنميل بنصف الوجه',
        'حساسية حادة جداً',
        'chest pain',
        'severe shortness of breath',
        'loss of consciousness',
        'stroke symptoms',
        'uncontrolled bleeding',
    ];

    /**
     * Check patient symptom text for emergency warning signs.
     */
    public function detectEmergency(string $text, ?User $actor = null): ?array
    {
        $normalizedText = mb_strtolower($text);

        foreach ($this->emergencyKeywords as $keyword) {
            if (mb_strpos($normalizedText, mb_strtolower($keyword)) !== false) {
                // Log Safety Event
                SafetyEventLog::create([
                    'actor_id' => $actor?->id,
                    'event_type' => 'EMERGENCY_DETECTED',
                    'symptom_snippet' => mb_substr($text, 0, 255),
                    'action_taken' => 'EMERGENCY_WARNING_TRIGGERED',
                    'details_json' => [
                        'matched_keyword' => $keyword,
                        'timestamp' => now()->toIso8601String(),
                    ],
                    'ip_address' => request()->ip(),
                    'created_at' => now(),
                ]);

                return [
                    'is_emergency' => true,
                    'matched_keyword' => $keyword,
                    'warning_title_ar' => '⚠️ تنبيه طوارئ عاجل',
                    'warning_message_ar' => 'قد تشير الأعراض الموصوفة إلى حالة طبية عاجلة تتطلب التقييم الطبي الفوري. يرجى التوجه فوراً إلى أقرب مركز طوارئ أو التواصل مع الإسعاف المحلي.',
                    'disclaimer_ar' => 'المساعد الذكي أداة إرشادية فقط ولا يحل محل الرعاية الطبية الفورية.',
                ];
            }
        }

        return null;
    }
}
