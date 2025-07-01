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
                <!--            <div>-->
                <!--                <InputLabel for="email" value="Email" />-->
                <!--                <TextInput-->
                <!--                    id="email"-->
                <!--                    v-model="form.email"-->
                <!--                    type="email"-->
                <!--                    class="mt-1 block w-full"-->
                <!--                    required-->
                <!--                    autofocus-->
                <!--                    autocomplete="username"-->
                <!--                />-->
                <!--                <InputError class="mt-2" :message="form.errors.email" />-->
                <!--            </div>-->

                <!--            <div class="mt-4">-->
                <!--                <InputLabel for="password" value="Password" />-->
                <!--                <TextInput-->
                <!--                    id="password"-->
                <!--                    v-model="form.password"-->
                <!--                    type="password"-->
                <!--                    class="mt-1 block w-full"-->
                <!--                    required-->
                <!--                    autocomplete="current-password"-->
                <!--                />-->
                <!--                <InputError class="mt-2" :message="form.errors.password" />-->
                <!--            </div>-->

                <!--            <div class="block mt-4">-->
                <!--                <label class="flex items-center">-->
                <!--                    <Checkbox v-model:checked="form.remember" name="remember" />-->
                <!--                    <span class="ms-2 text-sm text-gray-600">Remember me</span>-->
                <!--                </label>-->
                <!--            </div>-->

                <NButton class="mt-4" type="primary" size="large" block :loading="form.processing" :disabled="form.processing" attr-type="submit" round>
                    Войти
                </NButton>

                <!--            <div class="flex items-center justify-end mt-4">-->
                <!--                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">-->
                <!--                    Forgot your password?-->
                <!--                </Link>-->


                <!--            </div>-->
            </NForm>
        </AuthenticationCard>
    </NaiveProvider>
</template>
