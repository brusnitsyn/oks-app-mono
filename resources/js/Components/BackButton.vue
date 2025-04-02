<script setup>
import {router} from "@inertiajs/vue3";
import {IconArrowLeft} from "@tabler/icons-vue";
const props = defineProps({
    parentRoute: String
})
const goBack = () => {
    if (window.history.length > 1) {
        window.history.back()
    } else {
        router.visit(props.parentRoute, {
            preserveState: true,
            preserveScroll: true,
        })
    }
}
</script>

<template>
    <NButton @click.prevent="goBack" secondary>
        <template #icon>
            <NIcon :component="IconArrowLeft" />
        </template>
        Назад
    </NButton>
</template>

<style scoped>

</style>
