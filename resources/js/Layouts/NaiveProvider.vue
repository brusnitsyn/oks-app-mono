<script setup>
import {NConfigProvider, NMessageProvider, NDialogProvider} from 'naive-ui'
import {ruRU, dateRuRU} from 'naive-ui'
</script>

<template>
    <NMessageProvider>
        <NConfigProvider :locale="ruRU" :date-locale="dateRuRU">
            <slot />
        </NConfigProvider>
    </NMessageProvider>
</template>

<style scoped>

</style>
