<?php

namespace App\Traits;

trait Filterable
{
    /**
     * Применяет фильтры к запросу с поддержкой отношений
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        foreach ($filters as $field => $value) {
            if (empty($value)) {
                continue;
            }

            // Проверяем, содержит ли поле точку (отношение)
            if (str_contains($field, '.')) {
                $this->applyRelationshipFilter($query, $field, $value);
            } elseif (method_exists($this, 'scope' . $this->toCamelCase($field))) {
                // Используем кастомный scope если он существует
                $query->{$this->toCamelCase($field)}($value);
            } elseif ($this->isFilterableField($field)) {
                // Стандартная фильтрация
                $this->applyBasicFilter($query, $field, $value);
            }
        }

        return $query;
    }

    /**
     * Применяет фильтр к полю отношения
     */
    protected function applyRelationshipFilter($query, string $field, $value): void
    {
        [$relation, $relatedField] = explode('.', $field, 2);

        if (!method_exists($this, $relation)) {
            return;
        }

        $query->whereHasPowerJoin($relation, function($join) use ($relatedField, $value) {
            $this->applyBasicFilter($join, $relatedField, $value);
        });
    }

    /**
     * Применяет базовый фильтр с учетом типа значения
     */
    protected function applyBasicFilter($query, string $field, $value): void
    {
        if (is_array($value)) {
            if (isset($value['from']) || isset($value['to'])) {
                // Диапазон значений
                $this->applyRangeFilter($query, $field, $value);
            } else {
                // Множественные значения
                $query->whereIn($field, $value);
            }
        } elseif (is_string($value) && str_contains($value, '%')) {
            // Поиск по like
            $query->where($field, 'like', $value);
        } else {
            // Точное совпадение
            $query->where($field, $value);
        }
    }

    /**
     * Применяет фильтр по диапазону
     */
    protected function applyRangeFilter($query, string $field, array $value): void
    {
        if (isset($value['from'])) {
            $query->where($field, '>=', $value['from']);
        }
        if (isset($value['to'])) {
            $query->where($field, '<=', $value['to']);
        }
    }

    /**
     * Проверяет, является ли поле фильтруемым
     */
    protected function isFilterableField(string $field): bool
    {
        if (property_exists($this, 'filterable') && is_array($this->filterable)) {
            return in_array($field, $this->filterable);
        }

        return in_array($field, $this->getFillable()) || str_contains($field, '.');
    }

    /**
     * Применяет сортировку с поддержкой отношений
     */
    public function scopeSort($query, ?string $sortField = null, ?string $sortDirection = 'asc')
    {
        if (!$sortField) {
            return $query;
        }

        if (str_contains($sortField, '.')) {
            return $query->orderByPowerJoins($sortField, $sortDirection);
        }

        if ($this->isSortableField($sortField)) {
            return $query->orderBy($sortField, $sortDirection);
        }

        return $query;
    }

    protected function isSortableField(string $field): bool
    {
        if (property_exists($this, 'sortable') && is_array($this->sortable)) {
            return in_array($field, $this->sortable);
        }

        return $this->isFilterableField($field);
    }

    protected function toCamelCase(string $input): string
    {
        // Заменяем "_" на пробелы, делаем слова с заглавной буквы, убираем пробелы
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }
}
