<?php

namespace Database\Seeders;

use App\Models\ControlCallResult;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ControlCallResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ControlCallResult::create([ 'name' => 'Успешный' ]);
        ControlCallResult::create([ 'name' => 'Не берет трубку/выключен телефон' ]);
        ControlCallResult::create([ 'name' => 'Пациент отказывается от предоставления информации' ]);
    }
}
