<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {IconChevronLeft, IconEdit, IconSquareRoundedPlus} from "@tabler/icons-vue";
import {computed, provide, ref} from "vue";
import {
    NButton,
    NCard,
    NFlex,
    NGi,
    NGrid, NGridItem,
    NIcon,
    NList,
    NListItem,
    NScrollbar,
    NSpace,
    NText,
    useThemeVars
} from "naive-ui";
import {format} from "date-fns";
import {router, useForm} from "@inertiajs/vue3";
import AppModal from "@/Components/AppModal.vue";
import UpdateModalForm from "@/Pages/Patient/Partials/UpdateModalForm.vue";
import EditControlCallButton from "@/Pages/Patient/Partials/EditControlCallButton.vue";
import DeleteMedcardForm from "@/Pages/Patient/Partials/DeleteMedcardForm.vue";
const props = defineProps({
    patient: {
        type: Object,
        required: true
    },
    medDrugsStatuses: {
        type: Array,
    },
    medDrugsPeriods: {
        type: Array,
    },
    lpus: {
        type: Array,
    },
    mkbs: {
        type: Array,
    },
    complications: {
        type: Array,
    },
    additionalTreatment: {
        type: Array
    },
    callResults: {
        type: Array
    },
    surveyResults: {
        type: Array
    },
    reasonCloses: {
        type: Array
    },
})
const propsData = ref({
    ...props
})

console.log(propsData.value)

provide('patient', {
    lpus: propsData.value.lpus,
    mkbs: propsData.value.mkbs,
    medDrugsStatuses: propsData.value.medDrugsStatuses,
    medDrugsPeriods: propsData.value.medDrugsPeriods,
    complications: propsData.value.complications,
    additionalTreatment: propsData.value.additionalTreatment,
    callResults: propsData.value.callResults,
    surveyResults: propsData.value.surveyResults,
    reasonCloses: propsData.value.reasonCloses
})

const fio = computed(() => `${propsData.value.patient.family} ${propsData.value.patient.name} ${propsData.value.patient.ot}`)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
function onDeletePatient() {
    showDeleteModal.value = true
}

function onRestorePatient() {
    useForm({})
        .put(route('patients.restore', { patient: propsData.value.patient.id }), {
            onSuccess: () => {
                router.visit(route('patients.show', { patient: propsData.value.patient.id }))
            }
        })
}

const hasDeleted = computed(() => propsData.value.patient.last_medcard.med_card_reason_close !== null ? true : false)
</script>

<template>
    <AppLayout :title="fio">
        <div class="max-w-7xl h-full mx-auto px-4 xl:relative">
            <div class="flex flex-col items-center justify-center h-auto xl:h-full">
                <NGrid :cols="5" :x-gap="16" :y-gap="16" item-responsive responsive="screen">
                    <NGi span="m:5 l:3">
                        <NSpace vertical class="xl:max-w-3xl" :size="16">
                            <NCard class="relative shadow" :style="{ '--tw-shadow': `0 0 4px 0 rgba(236, 102, 8, 0.5)` }">
                                <template #action>
                                    <NButton class="absolute top-2 left-0 -translate-x-1/2 shadow" :style="{ '--tw-shadow': `0 0 4px 0 rgba(236, 102, 8, 0.5)` }" :color="useThemeVars().value.cardColor" :text-color="useThemeVars().value.textColor3" circle @click="router.visit(route('patients.index'))">
                                        <template #icon>
                                            <NIcon :component="IconChevronLeft" />
                                        </template>
                                    </NButton>
                                    <NFlex justify="space-between" align="center">
                                        <NSpace vertical :size="0">
                                            <NText class="text-lg font-bold">
                                                {{ fio }}
                                            </NText>
                                            <NText v-if="hasDeleted">
                                                Снят по причине: {{ propsData.patient.last_medcard.med_card_reason_close.name }}
                                            </NText>
                                        </NSpace>

                                        <NButton v-if="!hasDeleted" type="error" @click="onDeletePatient">
                                            Снять с регистра
                                        </NButton>
                                        <NButton v-else type="info" @click="onRestorePatient">
                                            Вернуть в регистр
                                        </NButton>
                                    </NFlex>
                                </template>
                            </NCard>

                            <NCard title="Персональная информация" class="relative shadow" :style="{ '--tw-shadow': `0 0 4px 0 rgba(236, 102, 8, 0.5)` }">
                                <template v-if="!hasDeleted" #header-extra>
                                    <NButton text @click="showEditModal = true">
                                        <template #icon>
                                            <NIcon :component="IconEdit" />
                                        </template>
                                        Изменить
                                    </NButton>
                                </template>
                                <NList hoverable>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>СНИЛС</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.snils">{{ propsData.patient.snils }}</NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Дата рождения</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.brith_at">{{ format(propsData.patient.brith_at, 'dd.MM.yyyy') }}</NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Номер телефона</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.phone">{{ propsData.patient.phone }}</NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Дополнительный номер телефона</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.dop_phone">{{ propsData.patient.dop_phone }}</NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                </NList>
                            </NCard>

                            <NCard v-if="propsData.patient.last_medcard != null" title="Информация по заболеванию" class="shadow" :style="{ '--tw-shadow': `0 0 4px 0 rgba(32, 128, 240, 0.5)` }">
                                <NList hoverable>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Дата поступления в стационар</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.last_medcard.recipient_at">
                                                    {{ format(propsData.patient.last_medcard.recipient_at, 'dd.MM.yyyy') }}
                                                </NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Дата выписки из стационара</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.last_medcard.extract_at">
                                                    {{ format(propsData.patient.last_medcard.extract_at, 'dd.MM.yyyy') }}
                                                </NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Дата взятия на диспансерный учет</NText></NGi>
                                            <NGi>
                                                <NText v-if="propsData.patient.last_medcard.disp">
                                                    {{ format(propsData.patient.last_medcard.disp.start_at, 'dd.MM.yyyy') }}
                                                </NText>
                                                <NText v-else>
                                                    —
                                                </NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Основной диагноз</NText></NGi>
                                            <NGi>
                                                <NText>{{propsData.patient.last_medcard.mkb.ds}} {{ propsData.patient.last_medcard.mkb.name }}</NText>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Сопутствующий диагноз</NText></NGi>
                                            <NGi>
                                                <NSpace vertical :size="0">
                                                    <template v-for="accompanying in propsData.patient.last_medcard.mkb_accompanying" v-if="propsData.patient.last_medcard.mkb_accompanying.length">
                                                        <NText>{{ accompanying.name }}</NText>
                                                    </template>
                                                    <NText v-else>
                                                        —
                                                    </NText>
                                                </NSpace>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Осложнения</NText></NGi>
                                            <NGi>
                                                <NSpace vertical :size="0">
                                                    <template v-for="comp in propsData.patient.last_medcard.complications" v-if="propsData.patient.last_medcard.complications.length">
                                                        <NText>{{ comp.name }}</NText>
                                                    </template>
                                                    <NText v-else>
                                                        —
                                                    </NText>
                                                </NSpace>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                    <NListItem>
                                        <NGrid :cols="2">
                                            <NGi><NText>Лекарственные препараты выданы на срок</NText></NGi>
                                            <NGi>
                                                <NSpace vertical :size="0">
                                                    <NText v-if="propsData.patient.last_medcard.med_drugs_period">{{ propsData.patient.last_medcard.med_drugs_period.name }}</NText>
                                                    <NText v-else>
                                                        —
                                                    </NText>
                                                </NSpace>
                                            </NGi>
                                        </NGrid>
                                    </NListItem>
                                </NList>
                            </NCard>
                        </NSpace>
                    </NGi>
                    <NGi span="m:5 l:2">
                        <NSpace vertical :size="16">
<!--                            <NCard title="История диспансерных наблюдений" class="shadow" :style="{ '&#45;&#45;tw-shadow': `0 0 4px 0 rgba(236, 102, 8, 0.5)` }">-->
<!--                                <NList v-if="patient.disps && patient.disps.length">-->
<!--                                    <NScrollbar class="max-h-[360px]">-->
<!--                                        <NListItem v-for="disp in patient.disps" :key="disp.id">-->
<!--                                            <NTooltip>-->
<!--                                                {{ disp.main_diagnos?.name }}-->
<!--                                                <template #trigger>-->
<!--                                                    <NThing :title="`${format(new Date(disp.begin_at), 'dd.MM.yyyy')} - ${format(new Date(disp.end_at), 'dd.MM.yyyy')}`" class="px-4">-->
<!--                                                        <template #header-extra>-->
<!--                                                            <NButton secondary size="small" @click="showOlderDisp(disp.id)">-->
<!--                                                                Подробнее-->
<!--                                                            </NButton>-->
<!--                                                        </template>-->
<!--                                                    </NThing>-->
<!--                                                </template>-->
<!--                                            </NTooltip>-->
<!--                                        </NListItem>-->
<!--                                    </NScrollbar>-->
<!--                                </NList>-->
<!--                                <NEmpty v-else description="В истории диспансерных наблюдений пусто" class="pt-5 pb-4">-->
<!--                                    <template #extra>-->
<!--                                        <NButton size="small" @click="showAddDisp = true">-->
<!--                                            Добавить новое наблюдение-->
<!--                                        </NButton>-->
<!--                                    </template>-->
<!--                                </NEmpty>-->
<!--                            </NCard>-->
                            <NCard v-if="propsData.patient.last_medcard.control_call != null" title="Контрольные точки" class="shadow" :style="{ '--tw-shadow': `0 0 4px 0 rgba(32, 128, 240, 0.5)` }">
                                <NList :show-divider="false">
                                    <NScrollbar>
                                        <NListItem v-for="controlCall in propsData.patient.last_medcard.control_call" :key="controlCall.id" class="rounded min-h-[54px]" :style="`backgroundColor: ${controlCall.called_at != null ? '#7FE7C4' : ''}`">
                                            <NGrid cols="2" class="px-4">
                                                <NGridItem class="flex items-center gap-x-1">
                                                    <NText class="font-bold">
                                                        {{ controlCall.name }}
                                                    </NText>
                                                    <NText>
                                                        &middot; {{ format(controlCall.call_at, 'dd.MM.yyyy') }}
                                                    </NText>
                                                </NGridItem>
                                                <NGridItem class="flex items-center justify-end" align="end">
                                                    <EditControlCallButton v-if="!hasDeleted" :control-call="controlCall" />
                                                    <!--                    <LazySelectControlPointOption v-model="control_point.control_point_option_id" :disabled="control_point.control_point_option_id" @change="value => updateControlPoint(control_point.id, value)" /> -->
                                                </NGridItem>
                                            </NGrid>
                                        </NListItem>
                                    </NScrollbar>
                                </NList>
                            </NCard>
                        </NSpace>
                    </NGi>
                </NGrid>
            </div>
        </div>

        <AppModal v-model:show="showEditModal">
            <UpdateModalForm :data="patient" />
        </AppModal>

        <AppModal v-model:show="showDeleteModal">
            <DeleteMedcardForm :data="patient" />
        </AppModal>
    </AppLayout>
</template>

<style scoped>

</style>
