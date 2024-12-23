<script setup>
import {
    NForm,
    NTabs,
    NTabPane,
    NFormItemGi,
    NInput,
    NGrid,
    NButton,
    NFlex,
    NSpace,
    NIcon,
    NGi,
    NSelect
} from "naive-ui";
import {computed, inject, ref} from "vue";
import InputSnils from "@/Components/Inputs/InputSnils.vue";
import {IconCheck, IconArrowRight, IconArrowLeft, IconCancel} from "@tabler/icons-vue"
import InputPhone from "@/Components/Inputs/InputPhone.vue";
import AppDatePicker from "@/Components/AppDatePicker.vue";
import {useForm, usePage} from "@inertiajs/vue3";
const { updateTitle, updateShow } = inject('modal')
const { lpus, mkbs, medDrugsStatuses, medDrugsPeriods, complications, additionalTreatment } = inject('patient')

const actionButtonTitle = ref('')
const currentTabIndex = ref(0)
const tabs = ref([
    'Персональная информация',
    'Информация по заболеванию'
])
const historyTabs = ref([])
const isLastTab = ref(false)
const formRef = ref()

function updateTab(tabIndex) {
    switch (tabIndex) {
        case 0: {
            updateTitle(tabs.value[tabIndex])
            actionButtonTitle.value = 'Далее'
        }
            break
        case 1: {
            updateTitle(tabs.value[tabIndex])
            isLastTab.value = true
            actionButtonTitle.value = 'Обновить'
        }
            break
    }
}

const props = defineProps({
    data: {
        type: Object
    }
})
function onNextClick() {
    if (isLastTab.value) {

        formRef.value?.validate(
            (errors) => {
                if (!errors) {
                    form.put( route('patients.update', { patient: props.data.id }), {
                        onSuccess: () => updateShow(false),
                        onError: (err) => console.log(err)
                    })
                }
                else {
                    console.log(errors)
                }
            }
        )

        return
    }

    historyTabs.value.push(currentTabIndex.value)
    currentTabIndex.value = currentTabIndex.value + 1
    updateTab(currentTabIndex.value)
}
function onBackClick() {
    if (historyTabs.value.length > 0) {
        historyTabs.value.pop()
        currentTabIndex.value = currentTabIndex.value - 1
        isLastTab.value = false
        updateTab(currentTabIndex.value)
    }
}
function onCloseClick() {
    updateShow(false)
}

const form = useForm({
    patient: props.data,
    medcard: props.data.last_medcard
})

const messageDefault = 'Это поле обязательно!'

const rules = {
    'patient.family': [
        {
            required: true,
            message: messageDefault,
            trigger: ['blur', 'input']
        }
    ],
    'patient.name': [
        {
            required: true,
            message: messageDefault,
            trigger: ['blur', 'input']
        }
    ],
    'patient.ot': [
        {
            required: true,
            message: messageDefault,
            trigger: ['blur', 'input']
        }
    ],
    'patient.snils': [
        {
            required: true,
            message: messageDefault,
            trigger: ['blur', 'input']
        },
        {
            min: 14,
            message: 'Неверный номер СНИЛС!',
            trigger: ['blur', 'input']
        }
    ],
    'patient.phone': [
        {
            required: true,
            message: messageDefault,
            trigger: ['blur', 'input']
        }
    ],
    'patient.dop_phone': [
        {
            required: false,
            message: messageDefault
        }
    ],
    'patient.brith_at' : [
        {
            required: true,
            validator(rule, value) {
                if (!value) {
                    return new Error(messageDefault)
                }
            },
            trigger: ['blur', 'input']
        }
    ],


    'medcard.lpu_id': [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
    'medcard.recipient_at': [
        {
            required: true,
            validator(rule, value) {
                if (!value) {
                    return new Error(messageDefault)
                }
            },
            trigger: ['blur', 'input']
        }
    ],
    'medcard.extract_at': [
        {
            required: true,
            validator(rule, value) {
                if (!value) {
                    return new Error(messageDefault)
                }
            },
            trigger: ['blur', 'input']
        }
    ],
    'medcard.med_drugs_status_id': [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
    'medcard.med_drugs_period_id': [
        {
            type: 'number',
            validator(rule, value) {
                if (form.medcard.med_drugs_status_id === 1 && typeof value === 'undefined') {
                    return new Error(messageDefault)
                }
                return true
            },
            trigger: ['blur', 'change']
        }
    ],
    'medcard.mkb_id': [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
    'medcard.mkb_attendant_ids': [
        {
            type: 'array',
            required: false,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
    'medcard.med_card_additional_treatment_id': [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ],
}

const mkbAttendant = computed(() => mkbs.filter((mkb) => mkb.has_attendant === true))
const mkbMain = computed(() => mkbs.filter((mkb) => mkb.has_attendant === false).map((mkb) => ( { id: mkb.id, name: `${mkb.ds} ${mkb.name}` } )))

updateTab(0)
</script>

<template>
    <NForm :model="form" :rules="rules" ref="formRef">
    <NTabs animated v-model:value="currentTabIndex" @update:value="(value) => updateTab(value)" tab-class="!hidden" pane-class="!p-0 !px-0.5">
        <NTabPane :name="0">
                <NGrid cols="3" x-gap="6" y-gap="6">
                    <NFormItemGi label="Дата рождения" path="patient.brith_at">
                        <AppDatePicker v-model:value="form.patient.brith_at" />
                    </NFormItemGi>
                    <NFormItemGi label="СНИЛС" path="patient.snils">
                        <InputSnils v-model:value="form.patient.snils" />
                    </NFormItemGi>
                    <NFormItemGi />

                    <NFormItemGi label="Фамилия" path="patient.family">
                        <NInput placeholder="" v-model:value="form.patient.family" />
                    </NFormItemGi>
                    <NFormItemGi label="Имя" path="patient.name">
                        <NInput placeholder="" v-model:value="form.patient.name" />
                    </NFormItemGi>
                    <NFormItemGi label="Отчество" path="patient.ot">
                        <NInput placeholder="" v-model:value="form.patient.ot" />
                    </NFormItemGi>

                    <NFormItemGi label="Номер телефона" path="patient.phone">
                        <InputPhone v-model:value="form.patient.phone" />
                    </NFormItemGi>
                    <NFormItemGi span="2" label="Дополнительный номер телефона" path="patient.dop_phone">
                        <InputPhone v-model:value="form.patient.dop_phone" />
                    </NFormItemGi>
                </NGrid>
        </NTabPane>
        <NTabPane :name="1">

                <NGrid cols="3" x-gap="6" y-gap="6">
                    <NFormItemGi span="2" label="ЛПУ" path="medcard.lpu_id">
                        <NSelect v-model:value="form.medcard.lpu_id" :options="lpus" value-field="id" label-field="name" placeholder="" />
                    </NFormItemGi>
                    <NFormItemGi />

                    <NGi span="3">
                        <NGrid cols="2" x-gap="6" y-gap="6">
                            <NFormItemGi label="Дата поступления в стационар" path="medcard.recipient_at">
                                <AppDatePicker v-model:value="form.medcard.recipient_at" />
                            </NFormItemGi>
                            <NFormItemGi label="Дата выписки из стационара" path="medcard.extract_at">
                                <AppDatePicker v-model:value="form.medcard.extract_at" />
                            </NFormItemGi>
                        </NGrid>
                    </NGi>

                    <NFormItemGi span="3" label="Основной диагноз" path="medcard.mkb_id">
                        <NSelect v-model:value="form.medcard.mkb_id" filterable :options="mkbMain" value-field="id" label-field="name" placeholder="" />
                    </NFormItemGi>

                    <NFormItemGi span="3" label="Сопутствующий диагноз" path="medcard.mkb_attendant_ids">
                        <NSelect v-model:value="form.medcard.mkb_attendant_ids" multiple filterable :options="mkbAttendant" value-field="id" label-field="name" placeholder="" />
                    </NFormItemGi>

                    <NFormItemGi span="3" label="Осложнения" path="medcard.complication_ids">
                        <NSelect placeholder="" v-model:value="form.medcard.complication_ids" multiple filterable :options="complications" value-field="id" label-field="name" />
                    </NFormItemGi>

                    <NFormItemGi label="Лекарственные препараты" path="medcard.med_drugs_status_id">
                        <NSelect placeholder="" v-model:value="form.medcard.med_drugs_status_id" :options="medDrugsStatuses" value-field="id" label-field="name" />
                    </NFormItemGi>
                    <NFormItemGi span="2" label="Лекарственные препараты выданы на срок" path="medcard.med_drugs_period_id" :show-require-mark="form.medcard.med_drugs_status_id === 1">
                        <NSelect placeholder="" v-model:value="form.medcard.med_drugs_period_id" :options="medDrugsPeriods" :disabled="form.medcard.med_drugs_status_id !== 1" value-field="id" label-field="name" />
                    </NFormItemGi>

                    <NFormItemGi span="2" label="Необходимость дополнительного лечения" path="medcard.med_card_additional_treatment_id">
                        <NSelect placeholder="" v-model:value="form.medcard.med_card_additional_treatment_id" :options="additionalTreatment" value-field="id" label-field="name" />
                    </NFormItemGi>
                </NGrid>
        </NTabPane>
    </NTabs>
    </NForm>
    <!--Навигатор-->
    <div class="mt-4">
        <NFlex align="center" justify="space-between">
            <NButton secondary @click="onCloseClick">
                <template #icon>
                    <NIcon :component="IconCancel" />
                </template>
                Отмена
            </NButton>
            <NSpace align="center" size="medium">
                <NButton v-if="historyTabs.length > 0" type="primary" secondary @click="onBackClick">
                    <template #icon>
                        <NIcon :component="IconArrowLeft" />
                    </template>
                    Назад
                </NButton>
                <NButton type="primary" @click="onNextClick" :secondary="!isLastTab" icon-placement="right">
                    <template #icon>
                        <NIcon v-if="isLastTab" :component="IconCheck" />
                        <NIcon v-else :component="IconArrowRight" />
                    </template>
                    {{ actionButtonTitle }}
                </NButton>
            </NSpace>
        </NFlex>
    </div>
</template>

<style scoped>

</style>
