<script setup>

import {NFlex, NSpace} from "naive-ui";
</script>

<template>
    <NFlex>
        <NSpace align="center">
            <div class="h-[34px] w-[50px] rounded flex flex-col justify-center items-center border-4 bg-green-300 border-green-300" />
            <span>
        &ndash; Норма
      </span>
        </NSpace>
        <NSpace align="center">
            <div class="h-[34px] w-[50px] rounded flex flex-col justify-center items-center border-4 bg-green-300 border-cyan-500" />
            <span>
        &ndash; Требуется консультация
      </span>
        </NSpace>
        <NSpace align="center">
            <div class="h-[34px] w-[50px] rounded flex flex-col justify-center items-center border-4 bg-green-300 border-red-700" />
            <span>
        &ndash; Вызов СМП
      </span>
        </NSpace>
        <NSpace align="center">
            <div class="h-[34px] w-[50px] rounded flex flex-col justify-center items-center border-4 border-black" />
            <span>
        &ndash; Отказ от предоставления информации
      </span>
        </NSpace>
        <NSpace align="center">
            <div class="h-[34px] w-[50px] rounded flex flex-col justify-center items-center border-4 border-black">
                дд.мм
            </div>
            <span>
        &ndash; Пациент не берет трубку
      </span>
        </NSpace>
    </NFlex>
</template>

<style scoped>

</style>
