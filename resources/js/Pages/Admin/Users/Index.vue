<script setup>
import AppLayout from "@/Layouts/AppLayout.vue"
import {NButton, NDropdown, NIcon} from "naive-ui";
import {IconDots, IconPencil, IconSquareRoundedPlus, IconTrash} from "@tabler/icons-vue";
import EditUserModal from "@/Pages/Admin/Users/Partials/EditUserModal.vue";
import CreateUserModal from "@/Pages/Admin/Users/Partials/CreateUserModal.vue";
import {router, usePage} from "@inertiajs/vue3";
const props = defineProps({
    users: Array
})

const hasShowModalEdit = shallowRef(false)
const hasShowModalCreate = shallowRef(false)
const hasShowModelDelete = shallowRef(false)
const currentUserEditId = shallowRef(null)

const renderIcon = (icon) => {
    return () => {
        return h(NIcon, null, {
            default: () => h(icon)
        })
    }
}

const menuRowOptions = (row) => {
    return [
        {
            label: 'Редактировать',
            key: 'edit',
            icon: renderIcon(IconPencil),
            onClick: (row) => {
                currentUserEditId.value = row.id
                hasShowModalEdit.value = true
            }
        },
        {
            type: 'divider',
            show: !(row.id === usePage().props.auth.user.id)
        },
        {
            label: 'Удалить',
            key: 'delete',
            icon: renderIcon(IconTrash),
            onClick: (row) => {
                deleteUser(row.id)
            },
            show: !(row.id === usePage().props.auth.user.id)
        }
    ]
}

const menuRowRender = (row) => h(
    NDropdown,
    {
        trigger: 'click',
        options: menuRowOptions(row),
        placement: 'bottom-end',
        onSelect: (key, itm) => {
            itm.onClick(row)
        }
    },
    {
        default: () => h(
            NButton,
            {
                text: true
            },
            {
                default: () => h(
                    NIcon,
                    {
                        component: IconDots,
                        size: 20
                    }
                )
            }
        )
    }
)

const columns = shallowRef([
    {
        key: 'id',
        title: 'ИД',
        align: 'left'
    },
    {
        key: 'name',
        title: 'Имя',
        align: 'left'
    },
    {
        key: 'role_name',
        title: 'Роль',
        align: 'left'
    },
    {
        key: 'organization',
        title: 'Организация',
        align: 'left'
    },
    {
        key: 'menu',
        render: (row) => menuRowRender(row),
        align: 'right'
    }
])

const deleteUser = (userId) => {
    router.delete(route('admin.users.delete', { user: userId }), {
        onSuccess: () => {
            router.reload()
        },
        onError: (err) => {
            window.$message.error(err)
        }
    })
}
</script>

<template>
    <AppLayout title="Учетные записи">
        <template #header>
            Учетные записи
        </template>
        <template #headermore>
            <NButton type="primary" @click="hasShowModalCreate = true">
                <template #icon>
                    <NIcon :component="IconSquareRoundedPlus" />
                </template>
                Добавить учетную запись
            </NButton>
        </template>
        <NDataTable :columns="columns" :data="users" />
        <EditUserModal v-model:show="hasShowModalEdit" :user-id="currentUserEditId" />
        <CreateUserModal v-model:show="hasShowModalCreate" />
    </AppLayout>
</template>

<style scoped>

</style>
