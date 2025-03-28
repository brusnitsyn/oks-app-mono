<script setup>
import { computed } from 'vue';
import { NCard, NTabs, NTabPane, NSelect, NDynamicInput, NSpace, NInput } from 'naive-ui';

const props = defineProps({
    config: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:config']);

const config = computed({
    get: () => props.config,
    set: (value) => emit('update:config', value),
});

const availableTables = ref([
    { label: 'Пациенты', value: 'patients' },
    { label: 'Мед. карты', value: 'med_cards' },
    // Другие таблицы...
]);

const operatorOptions = [
    { label: '=', value: '=' },
    // Другие операторы...
];

const booleanOptions = [
    { label: 'AND', value: 'and' },
    { label: 'OR', value: 'or' },
];

const createField = () => '';
const createFilter = () => ({
    column: '',
    operator: '=',
    value: '',
    boolean: 'and',
});

const handleFieldUpdate = (index, newValue) => {
    const newFields = [...config.value.fields];
    newFields[index] = newValue;
    config.value = { ...config.value, fields: newFields };
};

const updateFilter = (index, field, newValue) => {
    const newFilters = [...config.value.filters];
    newFilters[index] = { ...newFilters[index], [field]: newValue };
    config.value = { ...config.value, filters: newFilters };
};
</script>

<template>
    <n-card title="Конструктор отчета" segmented>
        <n-tabs type="line">
            <n-tab-pane name="tables" tab="Таблицы">
                <n-select
                    v-model:value="config.tables"
                    multiple
                    filterable
                    placeholder="Выберите таблицы"
                    :options="availableTables"
                />
            </n-tab-pane>
            <n-tab-pane name="fields" tab="Поля">
                <n-dynamic-input
                    v-model:value="config.fields"
                    :on-create="createField"
                >
                    <template #default="{ value, index }">
                        <n-input
                            :value="value"
                            @update:value="handleFieldUpdate(index, $event)"
                            placeholder="Имя поля (table.column)"
                        />
                    </template>
                </n-dynamic-input>
            </n-tab-pane>
            <n-tab-pane name="filters" tab="Фильтры">
                <n-dynamic-input
                    v-model:value="config.filters"
                    :on-create="createFilter"
                >
                    <template #default="{ value, index }">
                        <n-space vertical>
                            <n-input
                                :value="value.column"
                                @update:value="updateFilter(index, 'column', $event)"
                                placeholder="Колонка (table.column)"
                            />
                            <n-select
                                :value="value.operator"
                                @update:value="updateFilter(index, 'operator', $event)"
                                :options="operatorOptions"
                                placeholder="Оператор"
                            />
                            <n-input
                                :value="value.value"
                                @update:value="updateFilter(index, 'value', $event)"
                                placeholder="Значение"
                            />
                            <n-select
                                :value="value.boolean"
                                @update:value="updateFilter(index, 'boolean', $event)"
                                :options="booleanOptions"
                                placeholder="Логический оператор"
                            />
                        </n-space>
                    </template>
                </n-dynamic-input>
            </n-tab-pane>
        </n-tabs>
    </n-card>
</template>
