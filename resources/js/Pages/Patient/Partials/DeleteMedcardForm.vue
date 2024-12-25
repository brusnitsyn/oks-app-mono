<script setup>
import {inject} from "vue";
import {NButton, NFlex, NForm, NFormItemGi, NGi, NGrid, NIcon, NSelect, NSpace} from "naive-ui";
import {IconCancel, IconCheck} from "@tabler/icons-vue";
import {useForm, usePage} from "@inertiajs/vue3";
import AppDatePicker from "@/Components/AppDatePicker.vue";

const { updateTitle, updateShow } = inject('modal')
updateTitle('Снятие с регистра')
const page = usePage()
const reasonCloses = ref({ ...page.props.reasonCloses })
const formRef = ref()

const props = defineProps({
    data: Object
})

const form = useForm({
    med_card_reason_close_id: null,
    closed_at: null
})



const messageDefault = 'Это поле обязательно!'
const rules = {
    closed_at: [
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
    med_card_reason_close_id: [
        {
            type: 'number',
            required: true,
            message: messageDefault,
            trigger: ['blur', 'change']
        }
    ]
}

function onCloseClick() {
    updateShow(false)
}

function onSuccessClick() {
    formRef.value?.validate(
        (errors) => {
            if (!errors) {
                form.delete(route('patients.delete', { 'patient': props.data.id }), {
                    onSuccess: () => {
                        window.$message.success('Пациент снят с регистра')
                        updateShow(false)
                    },
                })
            }
            else {
                console.log(errors)
            }
        }
    )
}
</script>

<template>
    <NForm :model="form" :rules="rules" ref="formRef">
        <NGrid cols="2" x-gap="6" y-gap="6">
            <NFormItemGi label="Дата снятия" path="closed_at">
                <AppDatePicker v-model:value="form.closed_at" />
            </NFormItemGi>
            <NFormItemGi label="Причина снятия" path="med_card_reason_close_id">
                <NSelect  placeholder="" :options="reasonCloses" v-model:value="form.med_card_reason_close_id" label-field="name" value-field="id"  />
            </NFormItemGi>

            <NGi span="2" class="mt-4 pb-7">
                <NFlex align="center" justify="space-between">
                    <NButton secondary @click="onCloseClick">
                        <template #icon>
                            <NIcon :component="IconCancel" />
                        </template>
                        Отмена
                    </NButton>
                    <NSpace align="center" size="medium">
                        <NButton type="error" icon-placement="right" :disabled="!form.isDirty && !form.hasErrors" @click="onSuccessClick">
                            <template #icon>
                                <NIcon :component="IconCheck" />
                            </template>
                            Снять
                        </NButton>
                    </NSpace>
                </NFlex>
            </NGi>
        </NGrid>
    </NForm>
</template>

<style scoped>

</style>
