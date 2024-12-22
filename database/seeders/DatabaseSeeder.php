<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LpuSeeder::class,
            ComplicationSeeder::class,
            MkbSeeder::class,
            MedCardAdditionalTreatmentSeeder::class,
            MedCardReasonCloseSeeder::class,
            MedDrugsStatusSeeder::class,
            MedDrugsPeriodSeeder::class
        ]);
    }
}
