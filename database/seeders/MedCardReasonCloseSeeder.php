<?php

namespace Database\Seeders;

use App\Models\MedCardReasonClose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedCardReasonCloseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (MedCardReasonClose::count() > 0) MedCardReasonClose::truncate();
        MedCardReasonClose::create([
            'name' => 'Личный отказ пациента',
        ]);
        MedCardReasonClose::create([
            'name' => 'Смерть пациента',
        ]);
        MedCardReasonClose::create([
            'name' => 'Прохождении последней контрольной точки (12 месяцев)',
        ]);
        MedCardReasonClose::create([
            'name' => 'Повторное событие',
        ]);
    }
}
