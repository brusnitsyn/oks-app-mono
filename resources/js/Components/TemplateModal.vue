<script setup>
import { ref, watch } from 'vue';
import { NModal, NTabs, NTabPane, NForm, NFormItem, NInput, NSwitch, NDivider, NSpace, NButton } from 'naive-ui';
import { router } from '@inertiajs/vue3';
import ReportBuilder from '@/Components/ReportBuilder.vue';
import SqlEditor from '@/Components/SqlEditor.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:show', 'created']);

const name = ref('');
const description = ref('');
const isShared = ref(false);
const saving = ref(false);
const config = ref({
    sql: '',
    params: [],
    tables: [],
    fields: [],
    filters: [],
});

const saveTemplate = () => {
    saving.value = true;
    router.post(route('api.report-templates.store'), {
        name: name.value,
        description: description.value,
        is_shared: isShared.value,
        type: config.value.sql ? 'sql' : 'builder',
        config: config.value,
    }, {
        onSuccess: () => {
            emit('created');
            show.value = false;
        },
        onError: (error) => {
            console.error('Ошибка:', error);
            window.$message.error(error[Object.keys(error)[0]]);
        },
        onFinish: () => {
            saving.value = false;
        }
    });
    // try {
    //     router.post(route('api.report-templates.store'), {
    //         name: name.value,
    //         description: description.value,
    //         is_shared: isShared.value,
    //         type: config.value.sql ? 'sql' : 'builder',
    //         config: config.value,
    //     }, {
    //         onError: (error) => {
    //             console.error('Ошибка:', error);
    //             window.$message.error('Произошла ошибка');
    //         }
    //     });
    //
    //     emit('created');
    //     show.value = false;
    // } catch (error) {
    //     console.error('Ошибка:', error);
    //     window.$message.error('Произошла ошибка');
    // } finally {
    //     saving.value = false;
    // }
};

const show = ref(props.show);

watch(() => props.show, (val) => {
    show.value = val;
});

watch(show, (val) => {
    emit('update:show', val);
    if (!val) {
        // Сброс формы при закрытии
        name.value = '';
        description.value = '';
        isShared.value = false;
        config.value = {
            sql: '',
            params: [],
            tables: [],
            fields: [],
            filters: [],
        };
    }
});
</script>

<template>
    <n-modal v-model:show="show" preset="card" style="width: 900px" title="Создать шаблон отчета">
        <n-tabs type="line">
            <n-tab-pane name="builder" tab="Конструктор">
                <report-builder v-model:config="config" />
            </n-tab-pane>
            <n-tab-pane name="sql" tab="SQL редактор">
                <sql-editor v-model:sql="config.sql" v-model:params="config.params" />
            </n-tab-pane>
        </n-tabs>

        <n-divider />

        <n-form @submit.prevent="saveTemplate">
            <n-form-item label="Название" required>
                <n-input v-model:value="name" />
            </n-form-item>
            <n-form-item label="Описание">
                <n-input v-model:value="description" type="textarea" />
            </n-form-item>
            <n-form-item label="Общий доступ">
                <n-switch v-model:value="isShared" />
            </n-form-item>
        </n-form>

        <template #footer>
            <n-space justify="end">
                <n-button @click="show = false">Отмена</n-button>
                <n-button type="primary" @click="saveTemplate" :loading="saving">
                    Сохранить шаблон
                </n-button>
            </n-space>
        </template>
    </n-modal>
</template>
