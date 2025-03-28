<script setup>
defineProps({
    template: {
        type: Object,
        required: true,
    },
});

defineEmits(['execute', 'delete']);
</script>

<template>
    <n-card :title="template.name" hoverable>
        <template #header-extra>
            <n-tag v-if="template.is_shared" type="info" size="small">Общий</n-tag>
            <n-tag v-else type="default" size="small">Личный</n-tag>
        </template>

        <n-ellipsis :line-clamp="2" class="text-gray-500">
            {{ template.description || 'Без описания' }}
        </n-ellipsis>

        <n-divider />

        <n-space justify="space-between">
            <n-text depth="3" class="text-sm">
                {{ template.type === 'sql' ? 'SQL' : 'Конструктор' }}
            </n-text>
            <n-text depth="3" class="text-sm">
                {{ new Date(template.created_at).toLocaleDateString() }}
            </n-text>
        </n-space>

        <template #footer>
            <n-space justify="end">
                <n-button secondary @click="$emit('execute', template)">
                    Запустить
                </n-button>
                <n-button
                    type="error"
                    ghost
                    @click="$emit('delete', template)"
                    :disabled="!$page.props.auth.user || $page.props.auth.user.id !== template.user_id"
                >
                    Удалить
                </n-button>
            </n-space>
        </template>
    </n-card>
</template>
