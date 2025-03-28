<script setup>
import { ref, watch } from 'vue';
import { NModal, NCard, NTabs, NTabPane, NForm, NFormItem, NInput, NSwitch, NDivider, NSpace, NButton } from 'naive-ui';
import ReportBuilder from './ReportBuilder.vue';
import SqlEditor from './SqlEditor.vue';
import {router} from "@inertiajs/vue3";

const props = defineProps(['show']);
const emit = defineEmits(['update:show', 'created']);

const name = ref('');
const description = ref('');
const isShared = ref(false);
const config = ref({
    sql: '',
    params: [],
    tables: [],
    fields: []
});

const saveTemplate = async () => {
    await router.post('/report-templates', {
        name: name.value,
        description: description.value,
        is_shared: isShared.value,
        type: config.value.sql ? 'sql' : 'builder',
        config: config.value
    });

    emit('created');
    emit('update:show', false);
};
</script>

<template>
    <n-modal v-model:show="show">
        <n-card style="width: 800px" title="Создать шаблон">
            <n-tabs type="line">
                <n-tab-pane name="builder" tab="Конструктор">
                    <report-builder v-model:config="config"/>
                </n-tab-pane>
                <n-tab-pane name="sql" tab="SQL редактор">
                    <sql-editor v-model:sql="config.sql" v-model:params="config.params"/>
                </n-tab-pane>
            </n-tabs>

            <n-divider/>

            <n-form>
                <n-form-item label="Название" required>
                    <n-input v-model:value="name"/>
                </n-form-item>
                <n-form-item label="Описание">
                    <n-input v-model:value="description" type="textarea"/>
                </n-form-item>
                <n-form-item label="Общий доступ">
                    <n-switch v-model:value="isShared"/>
                </n-form-item>
            </n-form>

            <template #footer>
                <n-space justify="end">
                    <n-button @click="show = false">Отмена</n-button>
                    <n-button type="primary" @click="saveTemplate">Сохранить</n-button>
                </n-space>
            </template>
        </n-card>
    </n-modal>
</template>
