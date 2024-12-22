<?php

namespace Database\Seeders;

use App\Models\MedDrugsPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedDrugsPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedDrugsPeriod::create([
            'name' => '1 месяц'
        ]);
        MedDrugsPeriod::create([
            'name' => '2 месяца'
        ]);
        MedDrugsPeriod::create([
            'name' => '3 месяца'
        ]);
    }
}
