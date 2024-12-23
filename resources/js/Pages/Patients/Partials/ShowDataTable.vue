<script setup>
import {h, inject, ref} from "vue";
import {NDataTable} from "naive-ui";
import {Link} from "@inertiajs/vue3";
import {format} from "date-fns";

function getClassForRowCallResult(result_call_id) {
    switch (result_call_id) {
        case 1:
            return 'bg-green-300 mx-3'
        case 2:
        case 3:
            return 'bg-transparent mx-3'
    }
}

function getClassForRowControlOption(control_point_option_id) {
    switch (control_point_option_id) {
        case 2:
        case 3:
            return 'border-green-300'
        case 4:
            return 'border-cyan-500'
        case 5:
        case 6:
            return 'border-red-700'
        case 1:
            return 'border-black'
        case null:
            return 'border-transparent'
    }
}

const defaultColumns = ref([
    {
        title: '№\nп/п',
        key: 'id',
        width: 70,
        sorter: 'default',
        sortOrder: false,
        align: 'center'
    },
    {
        title: 'Дата поступления',
        key: 'recipient_at',
        width: 110,
        sorter: 'default',
        sortOrder: false,
        align: 'center',
    },
    {
        title: 'ФИО',
        key: 'fio',
        sorter: 'default',
        sortOrder: false,
        width: 220,
        align: 'center',
        render(row) {
            return h(
                Link,
                {
                    href: route('patients.show', { patient: row.id }),
                    preserveState: true
                },
                {
                    default: () => `${row.family} ${row.name} ${row.ot}`,
                }
            )
        }
    },
    {
        title: 'Дата рождения',
        key: 'brith_at',
        width: 110,
        sorter: 'default',
        sortOrder: false,
        align: 'center',
    },
    {
        title: 'Диагноз',
        width: 90,
        align: 'center',
        key: 'ds',
    },
    {
        title: 'Диспансерное\nнаблюдение',
        width: 130,
        align: 'center',
        key: 'disp_status',
    },
    {
        title: 'Лекарственные\nпрепараты',
        width: 130,
        align: 'center',
        key: 'med_drugs_status',
    },
    {
        title: 'Дополнительное\nлечение',
        width: 130,
        align: 'center',
        key: 'additional_treatment',
    },
    {
        title: '3-й день',
        width: 70,
        key: 'day3',
        align: 'center',
        className: 'relative day3',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4  absolute inset-0 ${getClassForRowCallResult(row.day3.control_call_result_id)} ${getClassForRowControlOption(row.day3.survey_result_id)}`
                },
                {
                    default: () => (row.day3.control_call_result_id !== null && (row.day3.control_call_result_id === 1 || row.day3.control_call_result_id === 3)) ? '' : row.day3.control_call_result_id === 2 ? format(row.day3.called_at, 'dd.MM') : format(row.day3.call_at, 'dd.MM.yy')
                }
            )
        }
    },
    {
        title: '1 мес',
        width: 70,
        key: 'mes1',
        align: 'center',
        className: 'relative mes1',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 absolute inset-0 ${getClassForRowCallResult(row.mes1.control_call_result_id)} ${getClassForRowControlOption(row.mes1.survey_result_id)}`
                },
                {
                    default: () => (row.mes1.control_call_result_id !== null && (row.mes1.control_call_result_id === 1 || row.mes1.control_call_result_id === 3)) ? '' : row.mes1.control_call_result_id === 2 ? format(row.mes1.called_at, 'dd.MM') : format(row.mes1.call_at, 'dd.MM.yy')
                }
            )
        }
    },
    {
        title: '3 мес',
        width: 70,
        key: 'mes3',
        className: 'relative mes3',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 absolute inset-0 ${getClassForRowCallResult(row.mes3.control_call_result_id)} ${getClassForRowControlOption(row.mes3.survey_result_id)}`
                },
                {
                    default: () => (row.mes3.control_call_result_id !== null && (row.mes3.control_call_result_id === 1 || row.mes3.control_call_result_id === 3)) ? '' : row.mes3.control_call_result_id === 2 ? format(row.mes3.called_at, 'dd.MM') : format(row.mes3.call_at, 'dd.MM.yy')
                }
            )
        }
    },
    {
        title: '6 мес',
        width: 70,
        key: 'mes6',
        className: 'relative mes6',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4  absolute inset-0 ${getClassForRowCallResult(row.mes6.control_call_result_id)} ${getClassForRowControlOption(row.mes6.survey_result_id)}`
                },
                {
                    default: () => (row.mes6.control_call_result_id !== null && (row.mes6.control_call_result_id === 1 || row.mes6.control_call_result_id === 3)) ? '' : row.mes6.control_call_result_id === 2 ? format(row.mes6.called_at, 'dd.MM') : format(row.mes6.call_at, 'dd.MM.yy')
                }
            )
        }
    },
    {
        title: '12 мес',
        width: 70,
        key: 'mes12',
        className: 'relative mes12',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 absolute inset-0 ${getClassForRowCallResult(row.mes12.control_call_result_id)} ${getClassForRowControlOption(row.mes12.survey_result_id)}`
                },
                {
                    default: () => (row.mes12.control_call_result_id !== null && (row.mes12.control_call_result_id === 1 || row.mes12.control_call_result_id === 3)) ? '' : row.mes12.control_call_result_id === 2 ? format(row.mes12.called_at, 'dd.MM') : format(row.mes12.call_at, 'dd.MM.yy')
                }
            )
        }
    },
    {
        title: 'Номер телефона',
        width: 140,
        align: 'center',
        key: 'phone',
    },
])

function rowProps(row) {
    if (row.has_closed !== false) { return { class: '!bg-gray-200' } }
    return { }
}

const { patients } = inject('patients')
</script>

<template>
    <NDataTable
        class="max-h-[calc(100vh-314px)] min-h-[calc(100vh-314px)] h-[calc(100vh-314px)]"
        bordered
        :row-props="rowProps"
        :single-line="false"
        :data="patients"
        :columns="defaultColumns" />
</template>

<style scoped>
::v-deep(.n-data-table-th__title) {
    @apply leading-4;
}
::v-deep(td) {
    @apply !bg-transparent;
}
</style>
