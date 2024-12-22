<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {NButton, NIcon} from "naive-ui";
import {IconSquareRoundedPlus} from "@tabler/icons-vue";
import ShowDataTable from "@/Pages/Patients/Partials/ShowDataTable.vue";
import {provide, ref} from "vue";
import CreateModalForm from "@/Pages/Patients/Partials/CreateModalForm.vue";
import AppModal from "@/Components/AppModal.vue";

const props = defineProps({
    patients: {
        type: Array
    },
    medDrugsStatuses: {
        type: Array
    },
    medDrugsPeriods: {
        type: Array
    },
    lpus: {
        type: Array
    },
    mkbs: {
        type: Array
    },
    complications: {
        type: Array
    },
    additionalTreatment: {
        type: Array
    }
})
provide('patients', {
    lpus: props.lpus,
    mkbs: props.mkbs,
    medDrugsStatuses: props.medDrugsStatuses,
    medDrugsPeriods: props.medDrugsPeriods,
    complications: props.complications,
    additionalTreatment: props.additionalTreatment,
    patients: props.patients
})
const showModalCreatePatient = ref(false)
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

        <ShowDataTable />

        <AppModal v-model:show="showModalCreatePatient">
            <CreateModalForm />
        </AppModal>
    </AppLayout>
</template>

<style scoped>
::v-deep(.n-data-table-wrapper) {
 @apply h-[calc(100vh-248px)] bg-white;
}
</style>
