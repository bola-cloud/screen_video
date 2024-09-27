<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TvsSeeder extends Seeder
{
    public function run(): void
    {
        // Data from the image (hospital name => number of screens)
        $hospitals = [
            'مستشفى الملك عبدالعزيز' => 44,
            'مراكز مستشفى الملك عبدالعزيز' => 1,
            'مركز التأهيل بمستشفى الملك عبدالعزيز' => 12,
            'مستشفى الصحة النفسية' => 12,
            'مستشفى الثغر' => 20,
            'مراكز مستشفى الثغر' => 17,
            'مستشفى العزيز للاطفال' => 4,
            'مستشفى شرق جدة' => 80,
            'مراكز مستشفى شرق جدة' => 0,
            'مستشفى الامل' => 13,
            'مجمع الملك عبدالله' => 85,
            'مستشفى العيون' => 14,
            'مركز الاسنان التخصصي' => 22,
            'مراكز مجمع الملك عبدالله' => 1,
            'مستشفى الولادة والاطفال التخصصي' => 1,
            'مستشفى الملك فهد' => 104,
        ];

        $screen_id = 1; // Start screen_id from 1 and increment it manually

        foreach ($hospitals as $hospital => $screenCount) {
            for ($i = 1; $i <= $screenCount; $i++) {
                DB::table('tvs')->insert([
                    'name' => "$hospital $i",
                    'screen_id' => $screen_id++, // Increment screen_id for each row
                    'location' => "location $i",
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}