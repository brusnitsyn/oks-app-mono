<?php

namespace Database\Seeders;

use App\Models\SurveyResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveyResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SurveyResult::create([
            'name' => 'Без результата'
        ]);
        SurveyResult::create([
            'name' => 'Норма'
        ]);
        SurveyResult::create([
            'name' => 'В пределах нормы'
        ]);
        SurveyResult::create([
            'name' => 'Требует консультации'
        ]);
        SurveyResult::create([
            'name' => 'Вызов врача на дом'
        ]);
        SurveyResult::create([
            'name' => 'Вызов СМП'
        ]);
    }
}
