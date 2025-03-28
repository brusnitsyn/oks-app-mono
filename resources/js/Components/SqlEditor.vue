<script setup>
import MonacoEditor from '@guolao/vue-monaco-editor';
import { NCard, NDivider } from 'naive-ui';
import ParamEditor from '@/Components/ParamEditor.vue';

const props = defineProps({
    sql: {
        type: String,
        required: true,
    },
    params: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['update:sql', 'update:params']);

const sql = computed({
    get: () => props.sql,
    set: (value) => emit('update:sql', value),
});

const params = computed({
    get: () => props.params,
    set: (value) => emit('update:params', value),
});

const editorOptions = {
    minimap: { enabled: false },
    fontSize: 14,
    automaticLayout: true,
};
</script>

<template>
    <n-card title="SQL редактор" segmented>
        <monaco-editor
            v-model:value="sql"
            language="sql"
            height="300px"
            :options="editorOptions"
        />

        <n-divider />

        <h3 class="text-lg font-medium mb-2">Параметры</h3>
        <param-editor v-model:params="params" />
    </n-card>
</template>
