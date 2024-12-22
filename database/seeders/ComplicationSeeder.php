<?php

namespace Database\Seeders;

use App\Models\Complication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Complication::create([
            'name' => 'ХСН (ФВ ≤40%)'
        ]);

        Complication::create([
            'name' => 'Фибрилляция предсердий'
        ]);
    }
}
