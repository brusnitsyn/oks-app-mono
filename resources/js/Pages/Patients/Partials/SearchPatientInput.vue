<script setup>
import { IconSearch } from '@tabler/icons-vue'
const value = defineModel('value')
const hasDisableSubmit = computed(() => value.value.length === 0)
const emits = defineEmits(['click'])
</script>

<template>
    <NInputGroup class="bg-white">
        <NInput v-model:value="value" class="max-w-screen-sm" size="large" placeholder="Найти пациента">
            <template #prefix>
                <NIcon :component="IconSearch" size="18" />
            </template>
        </NInput>
        <NButton size="large" :disabled="hasDisableSubmit" @click="$emit('click')">
            Поиск
        </NButton>
    </NInputGroup>
</template>

<style scoped>
:deep(.n-input .n-input__prefix) {
    @apply !mr-4;
}
</style>
