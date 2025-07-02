<script setup>
import AppModal from "@/Components/AppModal.vue";
import {router, useForm} from "@inertiajs/vue3";

const show = defineModel('show')
const props = defineProps({
    title: String,
    showTitle: {
        type: Boolean,
        default: true
    },
    form: {
        type: Boolean,
        default: false
    },
    userId: Number
})

const loadingContent = shallowRef(true)
const userData = shallowRef(null)
const roleData = shallowRef(null)
const organizationsData = shallowRef(null)
const userForm = ref({
    login: '',
    password: '',
    name: '',
    organization_id: null,
    role_id: null
})
const title = computed(() => {
    if (userData.value !== null)
        return userData.value.name
})

const prefetch = async () => {
    loadingContent.value = true
    Promise.all([fetchUser(), fetchOrganizations(), fetchRoles()])
        .then(function (results) {
            userData.value = results[0].data
            organizationsData.value = results[1].data
            roleData.value = results[2].data
            loadingContent.value = false
            userForm.value = results[0].data
        })
        .catch(err => {
            window.$message.error(err)
        })
}

const fetchUser = async () => {
    return axios.get(route('api.admin.users.get', { user: props.userId }))
}

const fetchOrganizations = async () => {
    return axios.get(route('api.organizations.index'))
}

const fetchRoles = async () => {
    return axios.get(route('api.roles.index'))
}

const closeModal = () => {
    show.value = false
}

const submitModal = () => {
    const form = useForm({...userForm.value})
    form.submit('put', route('admin.users.update', { user: props.userId }), {
        onSuccess: () => {
            show.value = false
        },
        onError: (err) => {
            window.$message.error(err)
        }
    })
}
</script>

<template>
    <AppModal v-model:show="show" :title="title" @after-enter="prefetch" @after-leave="loadingContent = true" class="relative max-w-screen-sm">
        <NSkeleton v-if="loadingContent" height="100%" class="absolute inset-0 z-50" />
        <NForm id="edit-form" :class="loadingContent ? 'collapse' : ''" @submit.prevent="submitModal">
            <NFormItem label="Логин">
                <NInput v-model:value="userForm.login" />
            </NFormItem>
            <NFormItem label="Пароль">
                <NInput v-model:value="userForm.password" />
            </NFormItem>
            <NFormItem label="Имя">
                <NInput v-model:value="userForm.name" />
            </NFormItem>
            <NFormItem label="Медицинская организация">
                <NSelect v-model:value="userForm.organization_id" :options="organizationsData" filterable />
            </NFormItem>
            <NFormItem label="Роль">
                <NSelect v-model:value="userForm.role_id" :options="roleData" filterable />
            </NFormItem>
        </NForm>

        <template #footer>
            <NFlex justify="space-between">
                <NButton @click="closeModal">
                    Отменить
                </NButton>
                <NButton attr-type="submit" form="edit-form" type="primary">
                    Сохранить изменения
                </NButton>
            </NFlex>
        </template>
    </AppModal>
</template>

<style scoped>

</style>
