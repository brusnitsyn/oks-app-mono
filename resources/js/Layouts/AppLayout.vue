<script setup>
import {computed, h, ref} from 'vue'
import {Head, Link, router, usePage} from '@inertiajs/vue3'
import {
    NLayout,
    NLayoutHeader,
    NLayoutSider,
    NLayoutFooter,
    NMenu,
    NH1,
    NSpace,
    NP,
    NFlex,
    NButton,
    NIcon,
    NMessageProvider, NImage
} from 'naive-ui'
import { IconSettings2, IconUserHexagon, IconUsers } from '@tabler/icons-vue'
import Banner from '@/Components/Banner.vue'
import NaiveProvider from "@/Layouts/NaiveProvider.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const menuCollapsed = ref(true)

function renderIcon(icon) {
    return () => h(NIcon, null, {default: () => h(icon)})
}

const menuOptions = [
    {
        label: () => h(
            Link,
            {
                href: '/',
            },
            {
                default: () => 'Регистр'
            }
        ),
        key: 'Patients',
        icon: renderIcon(IconUsers)
    }
]

const currentRoute = computed(() => {
    return router.page.component.substring(0, router.page.component.indexOf('/'))
})

const characterView = computed(() => usePage().component === 'Patient/Show')
const heartView = computed(() => usePage().component === 'Patients/Show')

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <NaiveProvider>
        <div>
            <Head :title="title"/>

            <Banner/>

            <div class="h-screen max-h-screen bg-gray-100">
                <NLayout position="absolute">
                    <NLayoutHeader class="p-4 px-[24px]" bordered>
                        <NFlex justify="space-between" align="center">
                            <NImage src="/assets/svg/logo.svg" preview-disabled object-fit="cover" :img-props="{class: 'w-full h-full max-h-[40px]' }" class="" />
                            <NSpace class="-m-5 -mr-[24px]" :size="0">
                                <NButton quaternary class=" w-[54px] h-[73px] rounded-none">
                                    <template #icon>
                                        <NIcon :component="IconSettings2" size="20"/>
                                    </template>
                                </NButton>
                                <NButton quaternary class=" w-[54px] h-[73px] rounded-none">
                                    <template #icon>
                                        <NIcon :component="IconUserHexagon" size="20"/>
                                    </template>
                                </NButton>
                            </NSpace>
                        </NFlex>
                    </NLayoutHeader>
                    <NLayout has-sider position="absolute" style="top: 73px; bottom: 54px">
                        <NLayoutSider collapse-mode="width" collapsed-width="0" width="240" :collapsed="menuCollapsed"
                                      show-trigger @collapse="menuCollapsed = true"
                                      @expand="menuCollapsed = false"
                                      :collapsed-trigger-class="menuCollapsed === true ? '!-right-5 !top-1/4' : ''"
                                      trigger-class="!top-1/4" bordered content-class="">
                            <NFlex vertical justify="space-between" class="h-full relative">
                                <NMenu :options="menuOptions" :value="currentRoute"/>
                                <NSpace vertical>
                                    <NImage v-if="characterView" :preview-disabled="true" class="h-[450px] p-8 px-8" src="/assets/svg/woman.svg" />
                                    <NImage v-if="heartView" :preview-disabled="true" src="/assets/svg/heart.svg" class="absolute bottom-[15%] px-6" />
                                </NSpace>
                            </NFlex>
                        </NLayoutSider>
                        <NLayout :content-class="menuCollapsed ? 'p-7 pl-14' : 'p-7'" class="" style="background-image: url('/assets/svg/bg.svg')">
                            <main>
                                <NFlex v-if="$slots.header || $slots.headermore" justify="space-between" align="center"
                                       class="mb-5">
                                    <NH1 v-if="$slots.header" class="!mb-0">
                                        <slot name="header"/>
                                    </NH1>
                                    <NSpace>
                                        <slot name="headermore"/>
                                    </NSpace>
                                </NFlex>
                                <NP v-if="$slots.subheader">
                                    <slot name="subheader"/>
                                </NP>
                                <NMessageProvider>
                                    <slot/>
                                </NMessageProvider>
                            </main>
                        </NLayout>
                    </NLayout>
                    <NLayoutFooter
                        bordered
                        position="absolute"
                        class="p-4 px-[24px]"
                    >
                        &copy;
                        <Link href="https://aokb28.su">Амурская областная клиническая больница</Link>
                        2024
                    </NLayoutFooter>
                </NLayout>
            </div>
        </div>
    </NaiveProvider>
</template>
