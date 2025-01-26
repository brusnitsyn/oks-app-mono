<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {IconSquareRoundedPlus} from "@tabler/icons-vue";
import ShowDataTable from "@/Pages/Patients/Partials/ShowDataTable.vue";
import CreateModalForm from "@/Pages/Patients/Partials/CreateModalForm.vue";
import AppModal from "@/Components/AppModal.vue";
import DataTableLegend from "@/Pages/Patients/Partials/DataTableLegend.vue";
import SearchPatientInput from "@/Pages/Patients/Partials/SearchPatientInput.vue";
import debounce from "@/Utils/debounce.js";
import {router, usePage} from "@inertiajs/vue3";

const page = usePage()
const showModalCreatePatient = ref(false)

const searchValue = ref(page.props.ziggy.query.search_value ?? '')
const searchValueDebounce = computed({
    get() {
        return searchValue.value
    },
    set(value) {
        searchValue.value = value
        debounce(() => {
            // router.get(route('patients.index'), { search_field: 'fio', search_value: value }, {
            //     preserveState: true,
            // })

            onSearch()
        }, 800)
    }
})

function onSearch() {
    router.reload({
        data: { search_field: 'full_name', search_value: searchValue.value },
        only: ['patients']
    })
}
</script>

<template>
    <AppLayout title="Главная">
        <template #header>
            Регистр пациентов с ОКС
        </template>
        <template #headermore>
            <NButton type="primary" @click="showModalCreatePatient = true">
                <template #icon>
                    <NIcon :component="IconSquareRoundedPlus" />
                </template>
                Добавить пациента
            </NButton>
        </template>

        <template #subheader>
            <NSpace vertical>
                <SearchPatientInput v-model:value="searchValueDebounce" @click="onSearch" />
                <DataTableLegend />
            </NSpace>
        </template>

        <ShowDataTable />

        <AppModal v-model:show="showModalCreatePatient">
            <CreateModalForm />
        </AppModal>
    </AppLayout>
</template>

<style scoped>

</style>
