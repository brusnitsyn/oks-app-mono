<script setup>

import {NDatePicker} from "naive-ui";
const value = defineModel('value')
const datePickerRef = ref(null)
const handleFocus = () => {
    const input = datePickerRef.value?.$el?.querySelector('input')
    if (input) input.focus()
}
</script>

<template>
    <NDatePicker
        ref="datePickerRef"
        @focus="handleFocus"
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
