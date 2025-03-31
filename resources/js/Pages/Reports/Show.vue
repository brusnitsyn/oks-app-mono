<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { NCard, NDivider } from 'naive-ui';
import AppLayout from '@/Layouts/AppLayout.vue';
import ReportParamsForm from '@/Components/ParamsForm.vue';
import ReportBuilderForm from '@/Components/BuilderForm.vue';
import ReportResults from '@/Components/Results.vue';

const props = defineProps({
    template: Object,
    results: Array,
});

const params = ref({})

const columns = computed(() => {
    if (!props.results || props.results.length === 0) return [];

    return Object.keys(props.results[0]).map(key => ({
        title: key,
        key: key
    }));
});

const executeSqlReport = (params) => {
    router.post(route('reports.execute', props.template.id), params, {
        preserveState: true
    });
};

const executeBuilderReport = (params) => {
    router.post(route('reports.execute', props.template.id), params);
};
</script>

<template>
    <app-layout :title="`Отчет: ${template.name}`">
        <template #header>
            Отчет: {{ template.name }}
        </template>

        <n-card>
            <report-params-form
                v-if="template.type === 'sql'"
                v-model:params="params"
                :template="template"
                @submit="executeSqlReport"
            />

            <report-builder-form
                v-else
                v-model:params="params"
                :template="template"
                @submit="executeBuilderReport"
            />

            <n-divider />

            <report-results
                v-if="results"
                :columns="columns"
                :data="results"
                :params="params"
            />
        </n-card>
    </app-layout>
</template>
