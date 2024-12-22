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
        MedCardReasonClose::create([
            'name' => 'Истечение времени',
        ]);
        MedCardReasonClose::create([
            'name' => 'Повторное событие',
        ]);
        MedCardReasonClose::create([
            'name' => 'Смерть',
        ]);
    }
}
