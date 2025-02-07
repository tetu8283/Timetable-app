<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subject::updateOrCreate(
            ['subject_id' => '000'],
            [
                'subject_name' => '休校',
                'school_id' => 'admin001',
                'color' => '#ff0000',
                'location' => ' '
            ]
        );

        Subject::updateOrCreate(
            ['subject_id' => '001'],
            [
                'subject_name' => 'テスト',
                'school_id' => 'admin001',
                'color' => '#ffff00',
                'location' => '各教室'
            ]
        );

        Subject::updateOrCreate(
            ['subject_id' => '002'],
            [
                'subject_name' => ' ',
                'school_id' => 'admin001',
                'color' => '#ffffff',
                'location' => ' '
            ]
        );
    }
}
