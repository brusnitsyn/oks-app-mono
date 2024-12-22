<?php

namespace Database\Seeders;

use App\Models\MedCardAdditionalTreatment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedCardAdditionalTreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedCardAdditionalTreatment::create([
            'name' => 'Не требуется',
            'short_name' => 'Не требуется'
        ]);
        MedCardAdditionalTreatment::create([
            'name' => 'Следующий этап ЧКВ',
            'short_name' => 'ЧКВ'
        ]);
        MedCardAdditionalTreatment::create([
            'name' => 'Контроль КАГ',
            'short_name' => 'КАГ'
        ]);
        MedCardAdditionalTreatment::create([
            'name' => 'АКШ',
            'short_name' => 'АКШ'
        ]);
        MedCardAdditionalTreatment::create([
            'name' => 'Консультация кардиохирурга',
            'short_name' => 'Консультация'
        ]);
    }
}
