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
    NImage, NAvatar, NText
} from 'naive-ui'
import {IconAdjustments, IconDoorExit, IconMenu3, IconReportAnalytics, IconUserCog, IconUsers} from '@tabler/icons-vue'
import Banner from '@/Components/Banner.vue'
import NaiveProvider from "@/Layouts/NaiveProvider.vue";
import {useStorage} from "@vueuse/core";
import {isLargeScreen, isMediumScreen, isSmallScreen} from "@/Utils/mediaQuery.js";
import {roles, useCheckRole} from "@/Composables/useRoleChecker.js";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

// const switchToTeam = (team) => {
//     router.put(route('current-team.update'), {
//         team_id: team.id,
//     }, {
//         preserveState: false,
//     });
// };

const menuCollapsed = useStorage('side-collapsed', false)
const mobileMenuCollapsed = ref(false)

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
    },
    {
        label: () => h(
            Link,
            {
                href: route('reports.index'),
            },
            {
                default: () => 'Отчеты'
            }
        ),
        key: 'Reports',
        icon: renderIcon(IconReportAnalytics)
    },
    {
        label: 'Администрирование',
        icon: renderIcon(IconAdjustments),
        show: useCheckRole(roles.ADMIN),
        children: [
            {
                label: () => h(
                    Link,
                    {
                        href: route('admin.users.index'),
                    },
                    {
                        default: () => 'Учетные записи'
                    }
                ),
                key: 'Admin',
                icon: renderIcon(IconUserCog)
            },
        ],
    },
]

const userOptions = [
    {
        label: 'Выйти из учетной записи',
        key: 'user-exit',
        icon: renderIcon(IconDoorExit),
        onClick: () => logout()
    },
]

const currentRoute = computed(() => {
    return router.page.component.substring(0, router.page.component.indexOf('/'))
})

const characterView = computed(() => usePage().component === 'Patient/Show')
const heartView = computed(() => usePage().component === 'Patients/Show' || usePage().component === 'Reports/Index' || usePage().component === 'Reports/Show')

const page = usePage()

const user = ref(page.props.auth.user)

const headerClass = computed(() => {
    console.log(isSmallScreen)
    if (isLargeScreen.value) {
        return '!mb-0'
    }
    if (isMediumScreen.value) {
        return '!mb-0 text-3xl'
    }
    if (isSmallScreen.value) {
        return '!mb-0 text-2xl'
    }
})

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <NaiveProvider>
        <div>
            <Head :title="title"/>

            <Banner/>

            <div class="h-screen max-h-screen bg-cover bg-no-repeat bg-fixed will-change-transform" style="background-image: url('/assets/svg/bg.svg');">
                <NLayout position="absolute" class="!bg-transparent">
                    <NLayoutHeader class="p-4 px-4 pr-[24px] lg:px-[24px]" bordered>
                        <NFlex justify="space-between" align="center">
                            <Link href="/" class="flex">
                                <NImage src="/assets/svg/logo.svg" preview-disabled :width="isLargeScreen ? '176.5' : '130'" />
                            </Link>
                            <NSpace class="-m-5 -mr-[24px]" :size="0">
                                <NDropdown v-if="user && isLargeScreen" trigger="click" placement="top-end" :options="userOptions" @select="(key, option) => option.onClick()">
                                    <NButton quaternary class="h-[73px] rounded-none">
                                        <NSpace align="center">
                                            <NSpace vertical align="end" :size="4">
                                                <NText class="font-semibold">
                                                    {{ user.name }}
                                                </NText>
                                                <NText>
                                                    {{ user.organization_name }}
                                                </NText>
                                            </NSpace>
                                            <NAvatar :src="user.profile_photo_url" round size="large" />
                                        </NSpace>
                                    </NButton>
                                </NDropdown>
                                <NButton v-if="!isLargeScreen" quaternary class="h-[61px] w-[61px] rounded-none" @click="mobileMenuCollapsed = true">
                                    <NIcon :component="IconMenu3" />
                                </NButton>
                            </NSpace>
                        </NFlex>
                    </NLayoutHeader>
                    <NLayout has-sider position="absolute" class="!bg-transparent" style="top: 73px; bottom: 54px">
                        <NLayoutSider v-if="isLargeScreen" collapse-mode="width" :collapsed-width="0" width="240" :collapsed="menuCollapsed"
                                      show-trigger @collapse="menuCollapsed = true"
                                      @expand="menuCollapsed = false"
                                      :collapsed-trigger-class="menuCollapsed === true ? '!-right-5 !top-1/4' : ''"
                                      trigger-class="!top-1/4" bordered content-class="">
                            <NFlex vertical justify="space-between" class="h-full relative">
                                <NMenu :options="menuOptions" :value="currentRoute"/>
                                <NSpace vertical>
                                    <NImage v-if="characterView" :preview-disabled="true" class="h-[450px] p-8 px-8" width="192" src="/assets/svg/woman.svg" />
                                    <NImage v-if="heartView" :preview-disabled="true" src="/assets/svg/heart.svg" width="192" class="absolute bottom-[15%] px-6" />
                                </NSpace>
                            </NFlex>
                        </NLayoutSider>
                        <NLayout :native-scrollbar="false" :content-class="(menuCollapsed && isLargeScreen) ? 'p-7 pl-14' : 'p-4 lg:p-7'" class="!bg-transparent">
                            <main>
                                <NFlex v-if="$slots.header || $slots.headermore" justify="space-between" align="center"
                                       class="mb-5">
                                    <NH1 v-if="$slots.header" :class="headerClass">
                                        <slot name="header"/>
                                    </NH1>
                                    <NSpace>
                                        <slot name="headermore"/>
                                    </NSpace>
                                </NFlex>
                                <NP v-if="$slots.subheader">
                                    <slot name="subheader"/>
                                </NP>
                                <slot/>
                            </main>
                        </NLayout>
                    </NLayout>
                    <NLayoutFooter
                        bordered
                        position="absolute"
                        class="p-4 px-[24px]"
                    >
                        &copy;
                        <a href="https://aokb28.su" target="_blank">Амурская областная клиническая больница</a>
                        2024 - 2025
                    </NLayoutFooter>
                </NLayout>
            </div>

            <NDrawer v-model:show="mobileMenuCollapsed" :placement="isMediumScreen ? 'left' : 'top'" width="280">
                <NDrawerContent header-class="!font-normal !text-base !leading-[1] !px-4 !pr-[24px]" body-content-class="!px-0 !py-0" class="">
                    <template #header>
                        <NFlex justify="space-between" align="center">
                            <Link href="/" class="flex">
                                <NImage src="/assets/svg/logo.svg" preview-disabled :width="isLargeScreen ? '176.5' : '130'" />
                            </Link>
                            <NSpace v-if="!isMediumScreen" class="-m-5 -mr-[24px]" :size="0">
                                <NButton quaternary class="h-[61px] w-[61px] rounded-none" @click="mobileMenuCollapsed = false">
                                    <NIcon :component="IconMenu3" />
                                </NButton>
                            </NSpace>
                        </NFlex>
                    </template>
                    <NMenu :options="menuOptions" :value="currentRoute" />
                </NDrawerContent>
            </NDrawer>
        </div>
    </NaiveProvider>
</template>

<style scoped>
:deep(.v-binder-follower-content >>> .n-popover-shared) {
    @apply !mt-0;
}
:deep(.n-menu-item-content) {
    @apply !pl-6;
}
</style>
