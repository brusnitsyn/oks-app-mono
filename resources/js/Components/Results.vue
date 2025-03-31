<script setup>
import { ref, computed } from 'vue';
import {
    NDataTable,
    NEmpty,
    NCard,
    NPagination,
    NButton,
    NSpace,
    NIcon,
    NText
} from 'naive-ui';
import { DownloadOutlined, FileExcelOutlined } from '@vicons/antd';
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    columns: Array,
    data: {
        type: Array,
        default: () => []
    },
    loading: Boolean,
    params: {}
});

const pagination = ref({
    page: 1,
    pageSize: 10,
    pageCount: computed(() => Math.ceil(props.data.length / pagination.value.pageSize)),
    showSizePicker: true,
    pageSizes: [10, 20, 50, 100],
    onChange: (page) => {
        pagination.value.page = page;
    },
    onUpdatePageSize: (pageSize) => {
        pagination.value.pageSize = pageSize;
        pagination.value.page = 1;
    }
});

const computedColumns = computed(() => {
    return props.columns.map(col => ({
        title: col.title,
        key: col.key,
        ellipsis: {
            tooltip: true
        },
    }));
});

const exportToCSV = () => {
    const headers = computedColumns.value.map(col => col.title).join(',');
    const rows = props.data.map(item =>
        computedColumns.value.map(col => `"${item[col.key]}"`).join(',')
    ).join('\n');

    const csvContent = `${headers}\n${rows}`;
    downloadFile(csvContent, 'report.csv', 'text/csv');
    window.$message.success('Отчет экспортирован в CSV');
};

const exportToExcel = async () => {
    const template  = usePage().props.template
    try {
        const response = await axios.post(
            `/api/report-templates/${template.id}/export`,
            { ...props.params },
            { responseType: 'blob' }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `report_${template.name}.xlsx`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Export failed:', error);
        window.$message.error('Ошибка при экспорте отчета');
    }
};

const downloadFile = (content, filename, mimeType) => {
    const blob = new Blob([content], { type: mimeType });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    URL.revokeObjectURL(link.href);
};
</script>

<template>
    <n-card title="Результаты отчета" v-if="data.length > 0">
        <n-data-table
            :columns="computedColumns"
            :data="data"
            :bordered="false"
            :pagination="pagination"
            :loading="loading"
            striped
        />

        <template #footer>
            <n-space justify="end">
                <n-space>
                    <n-button @click="exportToCSV" secondary>
                        <template #icon>
                            <n-icon><download-outlined /></n-icon>
                        </template>
                        CSV
                    </n-button>
                    <n-button @click="exportToExcel" type="primary" ghost>
                        <template #icon>
                            <n-icon><file-excel-outlined /></n-icon>
                        </template>
                        Excel
                    </n-button>
                </n-space>
            </n-space>
        </template>
    </n-card>

    <n-empty v-else description="Нет данных для отображения" class="mt-8">
        <template #extra>
            <n-text depth="3">Попробуйте изменить параметры отчета</n-text>
        </template>
    </n-empty>
</template>

<style scoped>
.n-data-table {
    margin-top: 16px;
}

.n-pagination {
    margin-top: 16px;
}
</style>
