<script setup>
import { NGrid, NGi, NCard, NTag, NEllipsis, NDivider, NSpace, NButton, NIcon, NText, NEmpty } from 'naive-ui';
import { PlayCircleOutlined, DeleteOutlined, CodeOutlined as CodeIcon, LayoutOutlined } from '@vicons/antd';
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    templates: {
        type: Array,
        required: true,
        default: () => []
    }
});

defineEmits(['execute', 'delete']);

const canDelete = (template) => {
    return template.user_id === usePage().props.auth.user?.id;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};
</script>

<template>
    <div class="template-list">
        <n-empty
            v-if="templates.length === 0"
            description="Нет доступных шаблонов"
            class="my-8"
        />

        <n-grid
            v-else
            cols="1 md:2 xl:3"
            x-gap="12"
            y-gap="12"
            responsive="screen"
        >
            <n-gi v-for="template in templates" :key="template.id">
                <n-card
                    :title="template.name"
                    hoverable
                    :class="{ 'shared-template': template.is_shared }"
                >
                    <template #header-extra>
                        <n-tag :type="template.is_shared ? 'info' : 'default'" size="small">
                            {{ template.is_shared ? 'Общий' : 'Личный' }}
                        </n-tag>
                    </template>

                    <n-ellipsis :line-clamp="2" class="template-description">
                        {{ template.description || 'Без описания' }}
                    </n-ellipsis>

                    <n-divider />

                    <div class="template-meta">
                        <n-space justify="space-between" align="center">
                            <n-tag size="small" round>
                                <template #icon>
                                    <n-icon>
                                        <code-icon v-if="template.type === 'sql'" />
                                        <layout-outlined v-else />
                                    </n-icon>
                                </template>
                                {{ template.type === 'sql' ? 'SQL' : 'Конструктор' }}
                            </n-tag>

                            <n-text depth="3" class="text-sm">
                                {{ formatDate(template.created_at) }}
                            </n-text>
                        </n-space>
                    </div>

                    <template #footer>
                        <n-space justify="end">
                            <n-button
                                secondary
                                @click="$emit('execute', template)"
                                class="execute-btn"
                            >
                                <template #icon>
                                    <n-icon><play-circle-outlined /></n-icon>
                                </template>
                                Запустить
                            </n-button>

                            <n-button
                                v-if="canDelete(template)"
                                type="error"
                                ghost
                                @click="$emit('delete', template)"
                                class="delete-btn"
                            >
                                <template #icon>
                                    <n-icon><delete-outlined /></n-icon>
                                </template>
                                Удалить
                            </n-button>
                        </n-space>
                    </template>
                </n-card>
            </n-gi>
        </n-grid>
    </div>
</template>

<style scoped>
.template-list {
    margin-top: 16px;
}

.template-description {
    color: var(--n-text-color);
    min-height: 44px;
}

.template-meta {
    font-size: 0.9rem;
}

.shared-template {
    border-left: 3px solid var(--n-info-color);
}

.execute-btn {
    background-color: var(--n-color-info);
}

.execute-btn:hover {
    background-color: var(--n-color-info-hover);
}

.delete-btn {
    transition: all 0.3s;
}

@media (max-width: 600px) {
    .template-meta {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
