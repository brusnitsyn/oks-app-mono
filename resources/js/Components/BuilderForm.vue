<template>
    <n-form ref="formRef" :model="formData" @submit.prevent="handleSubmit">
        <n-card title="Параметры отчета" segmented>
            <!-- Выбор таблиц -->
            <n-form-item label="Основная таблица" path="mainTable" required>
                <n-select
                    v-model:value="formData.mainTable"
                    :options="tableOptions"
                    placeholder="Выберите основную таблицу"
                    @update:value="handleMainTableChange"
                />
            </n-form-item>

            <!-- Связанные таблицы -->
            <n-form-item label="Дополнительные таблицы" path="joinedTables">
                <n-select
                    v-model:value="formData.joinedTables"
                    multiple
                    filterable
                    :options="joinableTables"
                    placeholder="Выберите таблицы для соединения"
                />
            </n-form-item>

            <!-- Поля для выборки -->
            <n-form-item label="Поля отчета" path="selectedFields" required>
                <n-dynamic-input
                    v-model:value="formData.selectedFields"
                    :on-create="createField"
                    #="{ value, index }"
                >
                    <n-select
                        :value="value"
                        @update:value="handleFieldChange(index, $event)"
                        :options="fieldOptions"
                        filterable
                        placeholder="Выберите поле (table.column)"
                        style="width: 100%"
                    />
                </n-dynamic-input>
            </n-form-item>

            <!-- Фильтры -->
            <n-form-item label="Условия фильтрации">
                <n-dynamic-input
                    v-model:value="formData.filters"
                    :on-create="createFilter"
                    #="{ value, index }"
                >
                    <n-space vertical style="width: 100%">
                        <n-select
                            :value="value.column"
                            @update:value="updateFilter(index, 'column', $event)"
                            :options="filterableFields"
                            placeholder="Колонка"
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
                            v-if="!['IS NULL', 'IS NOT NULL'].includes(value.operator)"
                        />
                    </n-space>
                </n-dynamic-input>
            </n-form-item>

            <n-button type="primary" attr-type="submit" :loading="submitting">
                Сформировать отчет
            </n-button>
        </n-card>
    </n-form>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import {
    NForm,
    NFormItem,
    NSelect,
    NInput,
    NDynamicInput,
    NSpace,
    NButton,
    NCard
} from 'naive-ui';

const props = defineProps({
    template: Object,
    tableStructure: Object
});

const emit = defineEmits(['submit']);

const formRef = ref(null);
const submitting = ref(false);

const formData = ref({
    mainTable: props.template?.config?.tables?.[0] || null,
    joinedTables: props.template?.config?.tables?.slice(1) || [],
    selectedFields: props.template?.config?.fields || [],
    filters: props.template?.config?.filters || []
});

const params = defineModel('params')

const operatorOptions = [
    { label: '=', value: '=' },
    { label: '!=', value: '!=' },
    { label: '>', value: '>' },
    { label: '<', value: '<' },
    { label: '>=', value: '>=' },
    { label: '<=', value: '<=' },
    { label: 'LIKE', value: 'LIKE' },
    { label: 'NOT LIKE', value: 'NOT LIKE' },
    { label: 'IN', value: 'IN' },
    { label: 'NOT IN', value: 'NOT IN' },
    { label: 'IS NULL', value: 'IS NULL' },
    { label: 'IS NOT NULL', value: 'IS NOT NULL' },
    { label: 'BETWEEN', value: 'BETWEEN' }
];

const tableOptions = computed(() => {
    return Object.keys(props.tableStructure || {}).map(table => ({
        label: table.replace(/_/g, ' '),
        value: table
    }));
});

const joinableTables = computed(() => {
    return tableOptions.value.filter(opt => opt.value !== formData.value.mainTable);
});

const fieldOptions = computed(() => {
    const options = [];

    // Поля основной таблицы
    if (formData.value.mainTable) {
        const fields = props.tableStructure?.[formData.value.mainTable] || [];
        fields.forEach(field => {
            options.push({
                label: `${formData.value.mainTable}.${field.name} (${field.type})`,
                value: `${formData.value.mainTable}.${field.name}`
            });
        });
    }

    // Поля связанных таблиц
    formData.value.joinedTables.forEach(table => {
        const fields = props.tableStructure?.[table] || [];
        fields.forEach(field => {
            options.push({
                label: `${table}.${field.name} (${field.type})`,
                value: `${table}.${field.name}`
            });
        });
    });

    return options;
});

const filterableFields = computed(() => {
    return fieldOptions.value.map(opt => ({
        ...opt,
        label: opt.label.split(' (')[0]
    }));
});

const createField = () => '';
const createFilter = () => ({
    column: null,
    operator: '=',
    value: ''
});

const handleMainTableChange = (newTable) => {
    formData.value.joinedTables = [];
    formData.value.selectedFields = [];
    formData.value.filters = [];
};

const handleFieldChange = (index, newValue) => {
    formData.value.selectedFields[index] = newValue;
};

const updateFilter = (index, field, newValue) => {
    formData.value.filters[index][field] = newValue;
};

const handleSubmit = () => {
    formRef.value?.validate((errors) => {
        if (!errors) {
            submitting.value = true;
            const config = {
                tables: [formData.value.mainTable, ...formData.value.joinedTables],
                fields: formData.value.selectedFields,
                filters: formData.value.filters
            };
            emit('submit', config);
            submitting.value = false;
        }
    });
};

// Правила валидации
const rules = {
    mainTable: {
        required: true,
        message: 'Выберите основную таблицу',
        trigger: 'blur'
    },
    selectedFields: {
        validator(rule, value) {
            return value.length > 0;
        },
        message: 'Добавьте хотя бы одно поле',
        trigger: 'blur'
    }
};
</script>

<style scoped>
.n-dynamic-input {
    width: 100%;
}
</style>
