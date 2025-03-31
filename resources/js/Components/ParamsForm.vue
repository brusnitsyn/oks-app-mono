<script setup>
import { ref, onMounted } from 'vue';
import { NForm, NFormItem, NInput, NDatePicker, NSelect, NButton, NSpace } from 'naive-ui';
import {router} from "@inertiajs/vue3";

const props = defineProps({
    template: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['submit']);

const params = defineModel('params')
const selectOptions = ref({});

// Инициализация параметров
onMounted(() => {
    props.template.config.params?.forEach(param => {
        params.value[param.name] = param.default || null;

        if (param.type === 'select' && param.query) {
            loadSelectOptions(param);
        }
    });
});

const getValidationRule = (param) => {
    const rules = [];
    if (param.required) {
        rules.push({ required: true, message: 'Обязательное поле' });
    }
    return rules;
};

const loadSelectOptions = async (param) => {
    try {
        if (param.query) {
            const response = await axios.post(
                route('report-templates.get-options'),
                { query: param.query }
            );

            selectOptions.value[param.name] = response.data.map(item => ({
                label: item.name || item,
                value: item.id || item
            }));
        }
    } catch (error) {
        console.error('Error loading options:', error);
    }
};

const getSelectOptions = (param) => {
    return selectOptions.value[param.name] || [];
};
</script>

<template>
    <n-form ref="form" :model="params" @submit.prevent="$emit('submit', params)">
        <n-space vertical>
            <n-form-item
                v-for="param in template.config.params"
                :key="param.name"
                :label="param.label || param.name"
                :path="param.name"
                :rule="getValidationRule(param)"
            >
                <n-input
                    v-if="param.type === 'string'"
                    v-model:value="params[param.name]"
                    :placeholder="param.default || ''"
                    clearable
                />

                <n-date-picker
                    v-else-if="param.type === 'date'"
                    v-model:value="params[param.name]"
                    type="date"
                    class="w-full"
                    clearable
                />

                <n-select
                    v-else-if="param.type === 'select'"
                    v-model:value="params[param.name]"
                    :options="getSelectOptions(param)"
                    filterable
                    clearable
                />
            </n-form-item>

            <n-button type="primary" attr-type="submit">
                Сформировать отчет
            </n-button>
        </n-space>
    </n-form>
</template>
