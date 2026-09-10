<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Seeder;

class AppointmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name_ar' => 'استشارة فيديو تفاعلية', 'name_en' => 'Video Call Consultation', 'code' => 'video'],
            ['name_ar' => 'استشارة صوتية عالية الجودة', 'name_en' => 'Audio Call Consultation', 'code' => 'audio'],
            ['name_ar' => 'استشارة نصية فورية', 'name_en' => 'Text Chat Consultation', 'code' => 'text'],
        ];

        foreach ($types as $type) {
            AppointmentType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'name_ar' => $type['name_ar'],
                    'name_en' => $type['name_en'],
                ]
            );
        }
    }
}
