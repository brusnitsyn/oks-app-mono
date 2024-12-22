<script setup>
import UpdateModalForm from "@/Pages/Patient/Partials/UpdateModalForm.vue";
import AppModal from "@/Components/AppModal.vue";
import {ref} from "vue";
import UpdateControlCall from "@/Pages/Patient/Partials/UpdateControlCall.vue";
import {NButton, NIcon} from "naive-ui";
import {IconEdit} from '@tabler/icons-vue'
const props = defineProps({
    controlCall: {
        type: Object
    }
})

const hasShowUpdateModal = ref(false)
</script>

<template>
    <NButton text @click="hasShowUpdateModal = true">
        <template #icon>
            <NIcon :component="IconEdit" />
        </template>
        Изменить
    </NButton>
    <AppModal v-model:show="hasShowUpdateModal">
        <UpdateControlCall :control-call="controlCall" />
    </AppModal>
</template>

<style scoped>

</style>
