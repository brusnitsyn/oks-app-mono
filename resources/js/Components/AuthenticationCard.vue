<script setup lang="ts">
import {NCard} from "naive-ui";
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <slot name="logo" />
        </div>

        <NCard class="max-w-md mt-6">
            <slot />
        </NCard>
<!--        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">-->
<!--            <slot />-->
<!--        </div>-->
    </div>
</template>
