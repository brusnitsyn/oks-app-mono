<script setup>
import AppModal from "@/Components/AppModal.vue"
import {router, useForm, usePage} from "@inertiajs/vue3";
const show = defineModel('show')
const loadingContent = shallowRef(true)
const organizationsData = shallowRef(null)
const form = useForm({
    organization_id: null
})
const prefetch = async () => {
    loadingContent.value = true
    await axios.get(route('api.organizations.index'))
        .then(res => {
            organizationsData.value = res.data
            form.organization_id = usePage().props.auth.user.organization_id
            loadingContent.value = false
        })
        .catch(err => {
            window.$message.error(err)
        })
}

const leaveModal = () => {
    loadingContent.value = true
}

const submit = () => {
    form.put(route('admin.change-organization'), {
        onSuccess: () => {
            router.visit(usePage().url)
        },
        onError: (err) => {
            window.$message.error(err)
        }
    })
}
</script>

<template>
    <AppModal v-model:show="show" @after-enter="prefetch" @after-leave="leaveModal" title="Переключиться на другую МО" class="max-w-md relative">
        <NForm id="form" :class="loadingContent ? 'collapse' : ''" @submit.prevent="submit">
            <NFormItem label="Медицинская организация">
                <NSelect v-model:value="form.organization_id" :options="organizationsData" filterable />
            </NFormItem>
        </NForm>

        <template #footer>
            <NButton attr-type="submit" form="form" type="primary" block>
                Переключиться
            </NButton>
        </template>

        <NSkeleton v-if="loadingContent" height="100%" class="absolute inset-0 z-50 rounded-lg" />
    </AppModal>
</template>

<style scoped>

</style>
