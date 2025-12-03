<script setup>
import {h, inject, ref} from "vue";
import {NDataTable} from "naive-ui";
import {Link, router, usePage} from "@inertiajs/vue3";
import {format} from "date-fns";

const page = usePage()

const patients = computed(() => page.props.patients)

function getClassForRowCallResult(result_call_id) {
    switch (result_call_id) {
        case 1:
            return 'bg-green-300 mx-2.5'
        case 2:
        case 3:
            return 'bg-transparent'
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
            return 'border-black mx-2'
        case null:
            return 'border-transparent'
    }
}

const defaultColumns = ref([
    {
        title: '№\nп/п',
        key: 'id',
        minWidth: 65,
        width: 65,
        sorter: true,
        sortOrder: false,
        align: 'center',
        render(row) {
            return h(
                'div',
                {},
                {
                    default: () => row.id
                }
            )
        }
    },
    {
        title: 'Дата выписки',
        key: 'lastMedcard.extract_at',
        width: 110,
        sorter: true,
        sortOrder: false,
        align: 'center',
        render(row) {
            return h(
                'div',
                {},
                {
                    default: () => row.extract_at ? format(row.extract_at, 'dd.MM.yyyy') : 'Ошибка'
                }
            )
        }
    },
    {
        title: 'ФИО',
        key: 'full_name',
        sorter: true,
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
                    default: () => row.full_name,
                }
            )
        }
    },
    {
        title: 'Дата рождения',
        key: 'brith_at',
        width: 110,
        sorter: true,
        sortOrder: false,
        align: 'center',
        render(row) {
            return h(
                'div',
                {},
                {
                    default: () => format(row.brith_at, 'dd.MM.yyyy')
                }
            )
        }
    },
    {
        title: 'Диагноз',
        width: 90,
        align: 'center',
        key: 'ds',
    },
    {
        title: 'Диспансерный\nучёт',
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
        width: 135,
        align: 'center',
        key: 'additional_treatment',
    },
    {
        title: '4-й день',
        width: 80,
        key: 'day3',
        align: 'center',
        className: 'relative day3 !p-0 h-[34px] !px-1',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 h-[34px] ${getClassForRowCallResult(row.day3.control_call_result_id)} ${getClassForRowControlOption(row.day3.survey_result_id)}`
                },
                {
                    default: () => (row.day3.control_call_result_id !== null && (row.day3.control_call_result_id === 1 || row.day3.control_call_result_id === 3)) ? '' : row.day3.control_call_result_id === 2 ? (row.day3.called_at ? format(row.day3.called_at, 'dd.MM') : 'Ошибка') : (row.day3.call_at ? format(row.day3.call_at, 'dd.MM.yy') : 'Ошибка')
                }
            )
        }
    },
    {
        title: '1 мес',
        width: 80,
        key: 'mes1',
        align: 'center',
        className: 'relative mes1 !p-0 h-[34px] !px-1',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 h-[34px] ${getClassForRowCallResult(row.mes1.control_call_result_id)} ${getClassForRowControlOption(row.mes1.survey_result_id)}`
                },
                {
                    default: () => (row.mes1.control_call_result_id !== null && (row.mes1.control_call_result_id === 1 || row.mes1.control_call_result_id === 3)) ? '' : row.mes1.control_call_result_id === 2 ? (row.mes1.called_at ? format(row.mes1.called_at, 'dd.MM') : 'Ошибка') : (row.mes1.call_at ? format(row.mes1.call_at, 'dd.MM.yy') : 'Ошибка')
                }
            )
        }
    },
    {
        title: '3 мес',
        width: 80,
        key: 'mes3',
        className: 'relative mes3 !p-0 h-[34px] !px-1',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 h-[34px] ${getClassForRowCallResult(row.mes3.control_call_result_id)} ${getClassForRowControlOption(row.mes3.survey_result_id)}`
                },
                {
                    default: () => (row.mes3.control_call_result_id !== null && (row.mes3.control_call_result_id === 1 || row.mes3.control_call_result_id === 3)) ? '' : row.mes3.control_call_result_id === 2 ? (row.mes3.called_at ? format(row.mes3.called_at, 'dd.MM') : 'Ошибка') : (row.mes3.call_at ? format(row.mes3.call_at, 'dd.MM.yy') : 'Ошибка')
                }
            )
        }
    },
    {
        title: '6 мес',
        width: 80,
        key: 'mes6',
        className: 'relative mes6 !p-0 h-[34px] !px-1',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 h-[34px] ${getClassForRowCallResult(row.mes6.control_call_result_id)} ${getClassForRowControlOption(row.mes6.survey_result_id)}`
                },
                {
                    default: () => (row.mes6.control_call_result_id !== null && (row.mes6.control_call_result_id === 1 || row.mes6.control_call_result_id === 3)) ? '' : row.mes6.control_call_result_id === 2 ? (row.mes6.called_at ? format(row.mes6.called_at, 'dd.MM') : 'Ошибка') : (row.mes6.call_at ? format(row.mes6.call_at, 'dd.MM.yy') : 'Ошибка')
                }
            )
        }
    },
    {
        title: '12 мес',
        width: 75,
        key: 'mes12',
        className: 'relative mes12 !p-0 h-[34px]',
        align: 'center',
        render(row) {
            return h(
                'div',
                {
                    class: `rounded m-1 flex flex-col justify-center items-center border-4 h-[34px] ${getClassForRowCallResult(row.mes12.control_call_result_id)} ${getClassForRowControlOption(row.mes12.survey_result_id)}`
                },
                {
                    default: () => (row.mes12.control_call_result_id !== null && (row.mes12.control_call_result_id === 1 || row.mes12.control_call_result_id === 3)) ? '' : row.mes12.control_call_result_id === 2 ? (row.mes12.called_at ? format(row.mes12.called_at, 'dd.MM') : 'Ошибка') : (row.mes12.call_at ? format(row.mes12.call_at, 'dd.MM.yy') : 'Ошибка')
                }
            )
        }
    },
    {
        title: 'Номер телефона',
        width: 150,
        align: 'center',
        key: 'phone',
    },
    {
        title: 'Адрес проживания',
        width: 150,
        align: 'center',
        key: 'address',
    },
])

function rowProps(row) {
    if (row.has_closed !== false) { return { class: '!bg-gray-200' } }
    return { }
}

function handleSorterChange(sorter) {
    const query = {
        sort_column: sorter.columnKey,
        sort_order: null
    }

    if (sorter.order === 'descend')
        query.sort_order = 'desc'
    else if (sorter.order === 'ascend')
        query.sort_order = 'asc'

    const column = defaultColumns.value.find(itm => itm.key === sorter.columnKey)
    column.sortOrder = !sorter ? false : sorter.order

    router.reload({
        data: {
            ...page.props.ziggy.query,
            ...query
        }
    })
}

const pagination = computed(() => {
    return {
        page: patients.value.current_page,
        pageSize: patients.value.per_page,
        pageCount: patients.value.last_page,
        itemCount: patients.value.total,
        onChange: (page) => {
            router.reload({
                data: {
                    ...usePage().props.ziggy.query,
                    page
                }
            })
            pagination.value.page = page
        },
        prefix() {
            return h(
                'div',
                {},
                [
                    h('div', {}, { default: () => `Кол-во пациентов: ${patients.value.total} / Кол-во выбывших пациентов: ${patients.value.total_closed}` }),
                ]
            )
        }
    }
})

const calculateScrollX = computed(() => {
    // Суммируем ширину всех колонок
    const totalWidth = defaultColumns.value.reduce((sum, col) => {
        return sum + (col.width || 100); // 100 - дефолтная ширина, если не задана
    }, 0);

    // Возвращаем общую ширину + 20% запаса
    return totalWidth * 1.05;
})
</script>

<template>
    <div class="table-container">
        <NDataTable
            remote
            ref="table"
            class="max-h-[calc(100vh-348px)] min-h-[calc(100vh-348px)] h-[calc(100vh-348px)]"
            flex-height
            bordered
            :scroll-x="calculateScrollX"
            style="min-width: 100%"
            @update:sorter="handleSorterChange"
            :row-props="rowProps"
            :single-line="false"
            :pagination="pagination"
            v-model:data="patients.data"
            :columns="defaultColumns">
        </NDataTable>
    </div>
</template>

<style scoped>
.table-container {
    width: 100%;
    overflow-x: auto;
}

::v-deep(.n-data-table-th__title) {
    @apply leading-4;
}
::v-deep(.n-data-table-wrapper) {
    @apply bg-white leading-5;
}
::v-deep(td) {
    @apply !bg-transparent;
}
</style>
