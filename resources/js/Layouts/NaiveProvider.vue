<script setup>
import {NConfigProvider} from 'naive-ui'
import {ruRU, dateRuRU} from 'naive-ui'
import NMessageContent from "@/Components/Naive/NMessageContent.vue";

const themeConfig = {
    common: {
        primaryColor: '#EC6608',
        primaryColorHover: '#f3731a',
        primaryColorPressed: '#dd6918',
        primaryColorSuppl: '#cf5e0f'
    }
}
</script>

<template>
    <NDialogProvider />

    <NConfigProvider :theme-overrides="themeConfig" :locale="ruRU" :date-locale="dateRuRU">
        <NMessageProvider keep-alive-on-hover>
            <NMessageContent />
        </NMessageProvider>
        <slot />
    </NConfigProvider>
</template>

<style scoped>

</style>
