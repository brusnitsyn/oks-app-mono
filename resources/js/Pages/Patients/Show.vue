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
import {roles, useCheckRole} from "@/Composables/useRoleChecker.js";

const page = usePage()
const showModalCreatePatient = ref(false)

const searchValue = ref(page.props.ziggy.query.search_value ?? '')
const patientSort = ref(page.props.ziggy.query.patient_sort ?? 'all')
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
        data: { search_field: 'full_name', search_value: searchValue.value, page: 1 },
        only: ['patients']
    })
}

const patientSortOptions = [
    {
        label: 'Все',
        value: 'all'
    },
    {
        label: 'Выбывшие',
        value: 'outcome'
    },
    {
        label: 'Просроченные',
        value: 'timeout'
    },
    {
        label: 'Позвонить сегодня',
        value: 'today'
    },
    {
        label: 'Позвонить завтра',
        value: 'next-day'
    },
]

function onSelectPatientSort(sort) {
    onApplyPatientFilters(searchValue.value, sort)
}

function onApplyPatientFilters(search_value, patient_sort) {
    router.reload({
        data: { search_field: 'full_name', search_value, patient_sort, page: 1, },
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
            <NButton v-if="useCheckRole(roles.ADMIN) || useCheckRole(roles.DOCTOR)" type="primary" @click="showModalCreatePatient = true">
                <template #icon>
                    <NIcon :component="IconSquareRoundedPlus" />
                </template>
                Добавить пациента
            </NButton>
        </template>

        <template #subheader>
            <NSpace vertical>
                <NFlex justify="space-between" align="center" :wrap="false">
                    <SearchPatientInput v-model:value="searchValueDebounce" @click="onSearch" />
                    <NFlex justify="end">
                        <NFormItem label="Контроль" label-placement="left" :show-feedback="false">
                            <NSelect class="w-[220px]"
                                     placeholder="Контроль"
                                     :options="patientSortOptions"
                                     v-model:value="patientSort"
                                     @update:value="v => onSelectPatientSort(v)"
                            />
                        </NFormItem>
                    </NFlex>
                </NFlex>
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
