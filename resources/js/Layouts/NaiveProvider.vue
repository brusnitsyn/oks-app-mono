<script setup>
import {NConfigProvider, NMessageProvider, NDialogProvider} from 'naive-ui'
import {ruRU, dateRuRU} from 'naive-ui'

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
    <NMessageProvider>
        <NConfigProvider :theme-overrides="themeConfig" :locale="ruRU" :date-locale="dateRuRU">
            <slot />
        </NConfigProvider>
    </NMessageProvider>
</template>

<style scoped>

</style>
