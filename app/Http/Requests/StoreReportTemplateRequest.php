<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => [
                'required',
                Rule::in(['sql', 'builder']),
            ],
            'is_shared' => 'boolean',
            'config' => 'required|array',
            'config.sql' => 'required_if:type,sql|max:10000',
            'config.params' => 'array',
            'config.params.*.name' => 'required|string|max:255',
            'config.params.*.type' => [
                'required',
                Rule::in(['string', 'number', 'date', 'boolean', 'select']),
            ],
            'config.params.*.label' => 'nullable|string|max:255',
            'config.params.*.required' => 'boolean',
            'config.params.*.default' => 'nullable|string|max:255',
            'config.params.*.query' => 'nullable|string|max:1000',
            'config.tables' => 'required_if:type,builder|array',
            'config.tables.*' => 'string|max:255',
            'config.fields' => 'required_if:type,builder|array',
            'config.fields.*' => 'string|max:255',
            'config.filters' => 'array',
            'config.filters.*.column' => 'required|string|max:255',
            'config.filters.*.operator' => [
                'required',
                Rule::in(['=', '!=', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'IS NULL', 'IS NOT NULL']),
            ],
            'config.filters.*.value' => 'nullable|string|max:255',
            'config.filters.*.boolean' => [
                'required',
                Rule::in(['and', 'or']),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Укажите название шаблона',
            'config.sql.required_if' => 'SQL запрос обязателен для SQL-шаблонов',
            'config.tables.required_if' => 'Выберите хотя бы одну таблицу для конструктора',
            'config.fields.required_if' => 'Добавьте хотя бы одно поле для конструктора',
            'config.params.*.name.required' => 'Укажите имя параметра',
            'config.filters.*.column.required' => 'Укажите колонку для фильтра',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Нормализация данных перед валидацией
        $this->merge([
            'is_shared' => $this->boolean('is_shared'),
            'config' => $this->config ?? [],
            'config.params' => $this->config['params'] ?? [],
            'config.filters' => $this->config['filters'] ?? [],
        ]);
    }
}
