<script setup lang="ts">
import {NModal} from "naive-ui";
import {provide, ref} from "vue";

const show = defineModel<boolean>('show')
const title = ref('')

function updateTitle(newTitle: string) {
    title.value = newTitle
}

function updateShow(newShow: boolean) {
    show.value = newShow
}

provide('modal', {
    updateTitle,
    updateShow
})
</script>

<template>
    <NModal v-model:show="show" :mask-closable="false" :title="title" preset="card" class="max-w-3xl" :bordered="false" size="huge">
        <slot />
        <template #footer>
            <slot name="footer" />
        </template>
    </NModal>
</template>

<style scoped>

</style>
