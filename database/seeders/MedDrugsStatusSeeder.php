<?php

namespace Database\Seeders;

use App\Models\MedDrugsStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedDrugsStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedDrugsStatus::create([
            'name' => 'Получены',
        ]);
        MedDrugsStatus::create([
            'name' => 'Не получены',
        ]);
    }
}
