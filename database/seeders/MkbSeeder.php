<?php

namespace Database\Seeders;

use App\Models\Mkb;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MkbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mkb::create([
            'ds' => 'I20.0',
            'name' => 'Нестабильная стенокардия (впервые возникшая, прогрессирующая)'
        ]);
        Mkb::create([
            'ds' => 'I21.0',
            'name' => 'Острый трансмуральный инфаркт передней стенки миокарда'
        ]);
        Mkb::create([
            'ds' => 'I21.1',
            'name' => 'Острый трансмуральный инфаркт нижней стенки миокарда'
        ]);
        Mkb::create([
            'ds' => 'I21.2',
            'name' => 'Острый трансмуральный инфаркт миокарда других уточненных локализаций'
        ]);
        Mkb::create([
            'ds' => 'I21.3',
            'name' => 'Острый трансмуральный инфаркт миокарда других неуточненной локализации'
        ]);
        Mkb::create([
            'ds' => 'I21.4',
            'name' => 'Острый субэндокардиальный инфаркт миокарда'
        ]);
        Mkb::create([
            'ds' => 'I22.0',
            'name' => 'Повторный инфаркт передней стенки миокарда'
        ]);
        Mkb::create([
            'ds' => 'I22.1',
            'name' => 'Повторный инфаркт нижней стенки миокарда'
        ]);
        Mkb::create([
            'ds' => 'I22.8',
            'name' => 'Повторный инфаркт миокарда другой уточненной локализации'
        ]);


        Mkb::create([
            'name' => 'Гипертоническая болезнь',
            'has_attendant' => true,
        ]);
        Mkb::create([
            'name' => 'Сахарный диабет',
            'has_attendant' => true,
        ]);
    }
}
