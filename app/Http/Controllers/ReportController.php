<?php

namespace App\Http\Controllers;

use App\Exports\ReportTemplateExport;
use App\Models\ReportTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    use AuthorizesRequests;

    private array $tableRelations = [
        // Здесь должны быть все связи между таблицами
        // Пример:
        'patients' => [
            'med_cards' => ['patients.id' => 'med_cards.patient_id'],
        ],
        // ...
    ];

    public function index()
    {
        return Inertia::render('Reports/Index', [
            'templates' => ReportTemplate::where('user_id', auth()->id())->get(),
            'sharedTemplates' => ReportTemplate::where('is_shared', true)
                ->get(),
        ]);
    }

    public function show(ReportTemplate $template)
    {
        $this->authorize('view', $template);

        return Inertia::render('Reports/Show', [
            'template' => $template,
            'results' => session('results'),
        ]);
    }

    public function execute(ReportTemplate $template)
    {
        $this->authorize('view', $template);

        $results = $template->type === 'sql'
            ? $this->executeSqlReport($template, request()->all())
            : $this->executeBuilderReport($template, request()->all());

        return redirect()->route('reports.show', $template)
            ->with('results', $results);
    }

    public function generate(Request $request, ReportTemplate $template)
    {
        $this->authorize('view', $template);

        $params = $this->validateParameters($request->all(), $template);

        if ($template->type === 'sql') {
            return $this->executeSqlReport($template, $params);
        }

        return $this->executeBuilderReport($template, $params);
    }

    private function validateParameters(array $data, ReportTemplate $template): array
    {
        $rules = collect($template->config['params'] ?? [])
            ->mapWithKeys(function ($param) {
                return [$param['name'] => $this->getValidationRules($param)];
            })
            ->toArray();

        return Validator::make($data, $rules)->validate();
    }

    private function getValidationRules(array $param): string
    {
        $rules = [];

        if ($param['required'] ?? false) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($param['type'] ?? 'string') {
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
            default:
                $rules[] = 'string';
        }

        return implode('|', $rules);
    }

    private function executeSqlReport(ReportTemplate $template, array $params)
    {
        try {
            // Преобразуем SQL в строку, если это Expression
            $sql = $template->config['sql'];
            if ($sql instanceof \Illuminate\Database\Query\Expression) {
                $sql = $sql->getValue();
            }

            // Проверяем, что SQL является строкой
            if (!is_string($sql)) {
                throw new \InvalidArgumentException('SQL query must be a string');
            }

            foreach ($params as $key => $param) {
                if (is_array($param)) {
                    $params[$key] = "{".implode(',', $param)."}";
                }
            }

            // Выполняем запрос
            return DB::select($sql, $params);
        } catch (\Exception $e) {
            Log::error("SQL report execution failed: " . $e->getMessage());
            abort(422, 'Ошибка выполнения SQL-запроса: ' . $e->getMessage());
        }
    }

    private function executeBuilderReport(ReportTemplate $template, array $params)
    {
        $config = $template->config;
        $mainTable = $config['tables'][0] ?? null;

        if (!$mainTable) {
            abort(400, 'Не указана основная таблица');
        }

        $query = DB::table($mainTable);

        // Добавляем JOIN для связанных таблиц
        foreach ($config['tables'] as $table) {
            if ($table === $mainTable) {
                continue;
            }

            try {
                $this->applyJoin($query, $mainTable, $table);
            } catch (\Exception $e) {
                Log::warning("Failed to join table {$table}: " . $e->getMessage());
            }
        }

        // Применяем фильтры
        foreach ($config['filters'] ?? [] as $filter) {
            $this->applyFilter($query, $this->replaceFilterPlaceholders($filter, $params));
        }

        // Выбираем указанные поля
        $selectFields = array_map(function ($field) use ($mainTable) {
            return str_contains($field, '.')
                ? $field
                : "{$mainTable}.{$field}";
        }, $config['fields'] ?? []);

        $query->select($selectFields);

        return $query->get();
    }

    private function applyJoin($query, string $mainTable, string $joinTable): void
    {
        if (isset($this->tableRelations[$mainTable][$joinTable])) {
            foreach ($this->tableRelations[$mainTable][$joinTable] as $left => $right) {
                $query->leftJoin($joinTable, $left, '=', $right);
            }
        } elseif (isset($this->tableRelations[$joinTable][$mainTable])) {
            foreach ($this->tableRelations[$joinTable][$mainTable] as $left => $right) {
                $query->leftJoin($joinTable, $right, '=', $left);
            }
        } else {
            throw new \RuntimeException("Не определена связь между таблицами '{$mainTable}' и '{$joinTable}'");
        }
    }

    private function applyFilter($query, array $filter): void
    {
        if (empty($filter['column']) || !isset($filter['operator'])) {
            return;
        }

        $column = $filter['column'];
        $table = null;

        if (str_contains($column, '.')) {
            [$table, $column] = explode('.', $column, 2);
        } else {
            $table = $query->from;
        }

        if (!Schema::hasTable($table)) {
            throw new \InvalidArgumentException("Таблица {$table} не существует");
        }

        if (!Schema::hasColumn($table, $column)) {
            throw new \InvalidArgumentException("Колонка {$column} не существует в таблице {$table}");
        }

        $qualifiedColumn = "{$table}.{$column}";
        $operator = strtoupper($filter['operator']);
        $value = $filter['value'] ?? null;
        $boolean = strtolower($filter['boolean'] ?? 'and');

        switch ($operator) {
            case 'IN':
            case 'NOT IN':
                $values = is_array($value) ? $value : explode(',', $value);
                $method = $operator === 'IN' ? 'whereIn' : 'whereNotIn';
                $query->{$method}($qualifiedColumn, $values, $boolean);
                break;

            case 'BETWEEN':
                if (is_string($value)) {
                    $values = array_map('trim', explode(',', $value, 2));
                } else {
                    $values = (array)$value;
                }

                if (count($values) === 2) {
                    $query->whereBetween($qualifiedColumn, $values, $boolean);
                }
                break;

            case 'IS NULL':
                $query->whereNull($qualifiedColumn, $boolean);
                break;

            case 'IS NOT NULL':
                $query->whereNotNull($qualifiedColumn, $boolean);
                break;

            case 'LIKE':
            case 'NOT LIKE':
                $value = str_replace(['%', '_'], ['\%', '\_'], $value);
                $method = $operator === 'LIKE' ? 'where' : 'whereNot';
                $query->{$method}($qualifiedColumn, 'LIKE', "%{$value}%", $boolean);
                break;

            default:
                if (!in_array($operator, ['=', '!=', '<>', '>', '<', '>=', '<='])) {
                    throw new \InvalidArgumentException("Неподдерживаемый оператор: {$operator}");
                }

                $query->where($qualifiedColumn, $operator, $value, $boolean);
        }
    }

    private function replaceFilterPlaceholders(array $filter, array $params): array
    {
        if (isset($filter['value'])) {
            $filter['value'] = preg_replace_callback('/\?([a-z0-9_]+)/i', function ($matches) use ($params) {
                return $params[$matches[1]] ?? $matches[0];
            }, $filter['value']);
        }

        return $filter;
    }

    public function export(ReportTemplate $template)
    {
        $this->authorize('view', $template);
        $params = request()->all();
        // Получаем данные отчета
        $results = $template->type === 'sql'
            ? $this->executeSqlReport($template, $params)
            : $this->executeBuilderReport($template, $params);

        // Генерируем имя файла
        $filename = $template->name.'.xlsx';

        // Возвращаем Excel файл
        return Excel::download(
            new ReportTemplateExport($params, $template->toArray(), $results, $this->getReportHeaders($template)),
            $filename
        );
    }

    protected function getReportHeaders(ReportTemplate $template): array
    {
        // Для SQL-шаблонов
        if ($template->type === 'sql') {
            return $this->getHeadersFromSql($template->config['sql']);
        }

        // Для Builder-шаблонов
        return array_map(function($field) {
            return str_contains($field, '.')
                ? explode('.', $field)[1]
                : $field;
        }, $template->config['fields']);
    }

    protected function getHeadersFromSql(string $sql): array
    {
        // Извлекаем часть SELECT из запроса
        preg_match('/SELECT(.*?)FROM/is', $sql, $matches);
        if (empty($matches[1])) {
            return [];
        }

        $selectPart = $matches[1];
        $columns = explode(',', $selectPart);

        $headers = [];
        foreach ($columns as $column) {
            $column = trim(preg_replace('/\s+/', ' ', $column));

            // 1. Явное указание AS с кавычками
            if (preg_match('/\s+as\s+"([^"]+)"/i', $column, $aliasMatches)) {
                $headers[] = $aliasMatches[1];
                continue;
            }

            // 2. AS без кавычек
            if (preg_match('/\s+as\s+([^\s,]+)/i', $column, $aliasMatches)) {
                $headers[] = $aliasMatches[1];
                continue;
            }

            // 3. Пропускаем технические столбцы без AS
            if (str_contains($column, 'to_char(to_timestamp')) {
                continue;
            }

            // 4. Для простых полей таблиц
            if (preg_match('/\.([^\s,]+)$/', $column, $fieldMatches)) {
                $headers[] = $fieldMatches[1];
                continue;
            }

            // 5. Все остальное пропускаем
        }

        return array_filter($headers);
    }
}
