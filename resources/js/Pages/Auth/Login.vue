<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {NButton, NForm, NFormItem, NImage, NInput} from "naive-ui";
import {ref} from "vue";
import NaiveProvider from "@/Layouts/NaiveProvider.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    login: '',
    password: '',
    remember: true,
});
const formRef = ref()
const defaultMessage = 'Это поле обязательно'
const rules = {
    login: [
        {
            required: true,
            message: defaultMessage,
            trigger: ['blur', 'input']
        }
    ],
    password: [
        {
            required: true,
            message: defaultMessage,
            trigger: ['blur', 'input'],
        }
    ]
}
function submit(e) {
    e.preventDefault()
    formRef.value?.validate(
        (errors) => {
            if (!errors) {
                form.transform(data => ({
                    ...data,
                    remember: form.remember ? 'on' : '',
                })).post(route('login'), {
                    onError: (error) => {
                        window.$message.error(Object.values(error)[0])
                    },
                    onFinish: () => form.reset('password'),
                });
            }
            else {
                console.log(errors)
            }
        }
    )
}
</script>

<template>
    <Head title="Авторизация" />

    <NaiveProvider>
        <AuthenticationCard>
            <template #logo>
                <NSpace vertical align="center" :size="16">
                    <NImage src="/assets/svg/logo-short.svg" preview-disabled />
                    <div class="rounded-3xl bg-white py-3 px-4 drop-shadow-sm text-[#ec6608] font-medium">
                        Регистр пациентов с ОКС
                    </div>
                </NSpace>
            </template>

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <NForm @submit.prevent="submit" :model="form" :rules="rules" ref="formRef">
                <NFormItem label="Имя пользователя" path="login">
                    <NInput v-model:value="form.login" size="large" placeholder="" round />
                </NFormItem>
                <NFormItem label="Пароль" path="password">
                    <NInput type="password" v-model:value="form.password" size="large" show-password-on="click" placeholder="" round />
                </NFormItem>

                <NButton class="mt-4" type="primary" size="large" block :loading="form.processing" :disabled="form.processing" attr-type="submit" round>
                    Войти
                </NButton>
            </NForm>
        </AuthenticationCard>
    </NaiveProvider>
</template>
