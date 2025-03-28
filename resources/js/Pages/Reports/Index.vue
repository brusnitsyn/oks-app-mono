<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { NCard, NButton, NIcon, NTabs, NTabPane } from 'naive-ui';
// import { PlusOutlined } from '@vicons/antd';
import AppLayout from '@/Layouts/AppLayout.vue';
import TemplateList from '@/Components/TemplateList.vue';
import TemplateModal from '@/Components/TemplateModal.vue';

const props = defineProps({
    templates: Array,
    sharedTemplates: Array,
});

const showTemplateModal = ref(false);

const executeReport = (template) => {
    router.visit(route('reports.show', template.id));
};

const deleteTemplate = async (template) => {
    router.delete(route('report-templates.destroy', template.id), {
        onSuccess: () => {
            router.reload();
        }
    });
};

const loadTemplates = () => {
    router.reload();
};
</script>

<template>
    <app-layout title="Отчеты">
        <template #header>
            Отчеты
        </template>

        <n-card title="Шаблоны отчетов">
            <template #header-extra>
                <n-button type="primary" @click="showTemplateModal = true">
                    <template #icon>
                        <n-icon><plus-outlined /></n-icon>
                    </template>
                    Создать шаблон
                </n-button>
            </template>

            <n-tabs type="line">
                <n-tab-pane name="templates" tab="Мои шаблоны">
                    <template-list
                        :templates="templates"
                        @execute="executeReport"
                        @delete="deleteTemplate"
                    />
                </n-tab-pane>
                <n-tab-pane name="shared" tab="Общие шаблоны">
                    <template-list
                        :templates="sharedTemplates"
                        @execute="executeReport"
                    />
                </n-tab-pane>
            </n-tabs>
        </n-card>

        <template-modal
            v-model:show="showTemplateModal"
            @created="loadTemplates"
        />
    </app-layout>
</template>
