<?php

namespace Database\Seeders;

use App\Models\ReportTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Пациенты на ДЛО',
                'description' => 'Пациенты на диспансерном учете',
                'type' => 'sql',
                'config' => [
                    "sql" => "SELECT
                    p.full_name,
                    p.snils,
                    p.phone,
                    m.num as card_number,
                    l.name as lpu_name,
                    s.name as drug_status,
                    m.recipient_at as start_date,
                    m.extract_at as end_date
                  FROM med_cards m
                  JOIN patients p ON m.patient_id = p.id
                  JOIN lpus l ON m.lpu_id = l.id
                  JOIN med_drugs_statuses s ON m.med_drugs_status_id = s.id
                  WHERE m.recipient_at BETWEEN :start_date AND :end_date
                  AND (:lpu_id IS NULL OR m.lpu_id = :lpu_id)
                  AND (:status_id IS NULL OR m.med_drugs_status_id = :status_id)",
                    "params" => [
                        [
                            "name" => "start_date",
                            "type" => "date",
                            "label" => "Дата начала",
                            "required" => true
                        ],
                        [
                            "name" => "end_date",
                            "type" => "date",
                            "label" => "Дата окончания",
                            "required" => true
                        ],
                        [
                            "name" => "lpu_id",
                            "type" => "select",
                            "label" => "ЛПУ",
                            "query" => "SELECT id, name FROM lpus ORDER BY name"
                        ],
                        [
                            "name" => "status_id",
                            "type" => "select",
                            "label" => "Статус препаратов",
                            "query" => "SELECT id, name FROM med_drugs_statuses ORDER BY name"
                        ]
                    ]
                ],
                'user_id' => 1,
                'is_shared' => true,
            ]
        ];

        foreach ($templates as $template) {
            ReportTemplate::create($template);
        }
    }
}
