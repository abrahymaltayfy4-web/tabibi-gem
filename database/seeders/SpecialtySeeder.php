<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            ['name_ar' => 'الطب العام والأسرة', 'name_en' => 'General & Family Medicine', 'code' => 'GEN_MED'],
            ['name_ar' => 'أمراض القلب والأوعية الدموية', 'name_en' => 'Cardiology', 'code' => 'CARDIO'],
            ['name_ar' => 'طب الأطفال وحنيثي الولادة', 'name_en' => 'Pediatrics', 'code' => 'PEDIATRICS'],
            ['name_ar' => 'الأمراض الجلدية والتناسلية', 'name_en' => 'Dermatology', 'code' => 'DERMATO'],
            ['name_ar' => 'أمراض النساء والتوليد', 'name_en' => 'Obstetrics & Gynecology', 'code' => 'OB_GYN'],
            ['name_ar' => 'أمراض الباطنية والجهاز الهضمي', 'name_en' => 'Gastroenterology', 'code' => 'GASTRO'],
            ['name_ar' => 'جراحة العظام والمفاصل', 'name_en' => 'Orthopedics', 'code' => 'ORTHO'],
            ['name_ar' => 'أمراض المخ والأعصاب', 'name_en' => 'Neurology', 'code' => 'NEUROLOGY'],
            ['name_ar' => 'الطب النفسي وعلاج الإدمان', 'name_en' => 'Psychiatry', 'code' => 'PSYCHIATRY'],
            ['name_ar' => 'أمراض العيون وجراحتها', 'name_en' => 'Ophthalmology', 'code' => 'OPHTHAL'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::firstOrCreate(
                ['code' => $specialty['code']],
                [
                    'name_ar' => $specialty['name_ar'],
                    'name_en' => $specialty['name_en'],
                    'is_active' => true,
                ]
            );
        }
    }
}
