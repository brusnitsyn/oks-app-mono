<script setup>
import { NDynamicInput, NInput, NSelect, NCheckbox, NSpace } from 'naive-ui';

const props = defineProps({
    params: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:params']);

const params = computed({
    get: () => props.params,
    set: (value) => emit('update:params', value),
});

const paramTypes = [
    { label: 'Текст', value: 'string' },
    { label: 'Число', value: 'number' },
    { label: 'Дата', value: 'date' },
    { label: 'Логический', value: 'boolean' },
    { label: 'Выпадающий список', value: 'select' },
];

const createParam = () => ({
    name: '',
    type: 'string',
    label: '',
    required: false,
    query: '',
});
</script>

<template>
    <n-dynamic-input
        v-model:value="params"
        :on-create="createParam"
    >
        <template #default="{ value, index }">
            <n-space vertical>
                <n-space align="center">
                    <n-input v-model:value="value.name" placeholder="Имя параметра" style="width: 200px" />
                    <n-select
                        v-model:value="value.type"
                        :options="paramTypes"
                        placeholder="Тип"
                        style="width: 150px"
                    />
                    <n-checkbox v-model:checked="value.required">Обязательный</n-checkbox>
                    <n-checkbox v-if="value.type === 'select'" v-model:checked="value.multiple">Множественный выбор</n-checkbox>
                </n-space>

                <n-input v-model:value="value.label" placeholder="Описание параметра" />

                <template v-if="value.type === 'select'">
                    <n-input
                        v-model:value="value.query"
                        type="textarea"
                        placeholder="SQL запрос (SELECT id, name FROM table) или значения через запятую"
                        :autosize="{ minRows: 2 }"
                    />
                </template>
            </n-space>
        </template>
    </n-dynamic-input>
</template>
