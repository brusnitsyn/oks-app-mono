<script setup>
import debounce from "@/Utils/debounce.js";

const value = defineModel('value')
const objectId = defineModel('objectid')
const options = ref()
const loading = ref(false)
function handleSearch(query) {
    if (!query.length) {
        options.value = []
        return
    }

    loading.value = true

    debounce(() => {
        axios.post('/api/fias', {
            search: query
        }).then((res) => {
            options.value = res.data
            loading.value = false
        })
    }, 400)
}

function updateValue(selectedValue) {
    const oId = options.value.find((itm) => itm.full_name === selectedValue)
    objectId.value = oId.objectid
}
</script>

<template>
    <NSelect v-model:value="value"
             @update-value="(v) => updateValue(v)"
             filterable
             placeholder=""
             :options="options"
             label-field="full_name"
             value-field="full_name"
             :loading="loading"
             clearable
             remote
             :clear-filter-after-select="false"
             @search="handleSearch"/>
</template>

<style scoped>

</style>
