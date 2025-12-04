<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected array $params;
    protected array $template;
    protected $data;
    protected $headers;

    public function __construct(array $params, array $template, array $data, array $headers)
    {
        $this->params = $params;
        $this->template = $template;
        $this->data = $data;
        $this->headers = $headers;
    }

    public function array(): array
    {
        return $this->transformData($this->data);
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function startCell(): string
    {
        return 'A5'; // Начинаем данные с 5 строки
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet;

                // Добавляем информацию об отчете
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', $this->template['name']); // 'Отчет по контрольным точкам пациентов'

                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Дата формирования: '. now()->format('d.m.Y H:i'));

                if (isset($this->params['period_start']) && isset($this->params['period_end'])) {
                    $sheet->mergeCells('A3:D3');
                    $sheet->setCellValue('A3', "Дата выписки с " . Carbon::createFromTimestampMs($this->params['period_start'])->timezone(config('app.timezone'))->format('d.m.Y') . ' по ' . Carbon::createFromTimestampMs($this->params['period_end'])->timezone(config('app.timezone'))->format('d.m.Y'));
                }

                if (isset($this->params['disp_status'])) {
                    $sheet->mergeCells('A3:D3');
                    $sheet->setCellValue('A3', "Статус диспансерного учёта: " . $this->params['disp_status']);
                }

                // Стили для заголовка отчета
                $sheet->getStyle('A1:A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
            AfterSheet::class => function(AfterSheet $event) {
                // Границы для всех заполненных ячеек
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();

                // Получаем реально используемый диапазон
                $usedRange = $sheet->calculateWorksheetDataDimension();

                $sheet->getStyle($usedRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Находим реальную последнюю колонку с данными
                $lastColumnWithData = $this->getLastColumnWithData($sheet, $lastRow);

                // Если найдена реальная последняя колонка, используем её
                if ($lastColumnWithData) {
                    $lastColumn = $lastColumnWithData;
                } else {
                    // Если не нашли, берем максимальную из заголовков
                    $lastColumn = $sheet->getHighestColumn();
                }

                if (isset($this->params['disp_status'])
                    || (isset($this->params['period_start'])
                        && isset($this->params['period_end'])))
                {
                    $filterRange = 'A4:' . $lastColumn . '4';
                    $sheet->freezePane('A5');
                } else {
                    $filterRange = 'A3:' . $lastColumn . '3';
                    $sheet->freezePane('A4');
                }

                $sheet->setAutoFilter($filterRange);
            },
        ];
    }

    // Метод для поиска реальной последней колонки с данными
    protected function getLastColumnWithData($sheet, $lastRow): ?string
    {
        // Проверяем строку с заголовками (4-я строка)
        $headerRow = 4;
        $highestColumn = $sheet->getHighestColumn();

        // Преобразуем букву колонки в число
        $maxColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Идем от последней колонки к первой, ищем первую непустую ячейку в строке заголовков
        for ($col = $maxColIndex; $col >= 1; $col--) {
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $cellValue = $sheet->getCell($columnLetter . $headerRow)->getValue();

            if (!empty($cellValue) || $cellValue === '0') {
                return $columnLetter;
            }
        }

        return null;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => [
                'alignment' => ['wrapText' => true]
            ],
        ];
    }

    protected function transformData(array $data): array
    {
        return $data;
    }
}
