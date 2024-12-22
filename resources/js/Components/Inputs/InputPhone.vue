<script setup>
import {NInput} from "naive-ui";

const value = defineModel('value`')
</script>

<template>
    <NInput v-model:value="value" v-mask="'+7 (###) ###-##-##'" placeholder="" />
</template>

<style scoped>

</style>
