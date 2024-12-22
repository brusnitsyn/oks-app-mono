<script setup>

import {NDatePicker} from "naive-ui";
const value = defineModel('value')
</script>

<template>
    <NDatePicker
        v-model:value="value"
        v-mask="'##.##.####'"
        placeholder=""
        format="dd.MM.yyyy"
        type="date"
        class="w-full"
    />
</template>

<style scoped>

</style>
