<?php

namespace Database\Seeders;

use App\Models\Lpu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LpuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lpu::create([
            'name' => 'РСЦ Благовещенск',
        ]);

        Lpu::create([
            'name' => 'РСЦ Свободный',
        ]);

        Lpu::create([
            'name' => 'ПСО Благовещенск',
        ]);

        Lpu::create([
            'name' => 'ПСО Райчихинск',
        ]);

        Lpu::create([
            'name' => 'ПСО Зея',
        ]);

        Lpu::create([
            'name' => 'ПСО Тында',
        ]);
    }
}
