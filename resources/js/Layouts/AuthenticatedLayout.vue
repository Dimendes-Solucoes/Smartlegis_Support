<script setup lang="ts">
import { computed, ref } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { SunIcon, MoonIcon, UserIcon, HomeIcon, ArrowLeftEndOnRectangleIcon, UsersIcon, NewspaperIcon, CalendarDateRangeIcon, BuildingOfficeIcon, Cog6ToothIcon, ClockIcon } from '@heroicons/vue/24/solid';

const isDark = ref(false);

const page = usePage();

const currentTenantCityName = computed(() => {
    return (page.props.tenant as any)?.city_name || 'Selecione uma cidade';
});

function applyTheme(theme: string) {
    if (theme === 'dark') {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
        isDark.value = true;
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
        isDark.value = false;
    }
}

if (localStorage.getItem("theme") === "dark") {
    applyTheme("dark");
} else {
    applyTheme("light");
}
</script>

<template>
    <div class="flex bg-gray-100 dark:bg-gray-900">
        <aside
            class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-screen top-0 left-0 z-50">
            <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700">
                <Link :href="route('tenant.settings')" class="flex items-center justify-center w-full h-full">
                <span class="text-md font-semibold text-gray-800 dark:text-gray-200">
                    {{ currentTenantCityName }}
                </span>
                </Link>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 flex flex-col">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <template #icon>
                        <HomeIcon class="h-5 w-5" />
                    </template>
                    Dashboard
                </NavLink>

                <NavLink :href="route('tenant.settings')" :active="route().current('tenant.settings')">
                    <template #icon>
                        <Cog6ToothIcon class="h-5 w-5" />
                    </template>
                    Configurações
                </NavLink>

                <NavLink :href="route('calendar.index')" :active="route().current('calendar.index')">
                    <template #icon>
                        <CalendarDateRangeIcon class="h-5 w-5" />
                    </template>
                    Calendário
                </NavLink>

                <NavLink :href="route('profile.edit')" :active="route().current('profile.edit')">
                    <template #icon>
                        <UserIcon class="h-5 w-5" />
                    </template>
                    Perfil
                </NavLink>

                <hr />

                <NavLink :href="route('users.index')" :active="route().current('users.index')">
                    <template #icon>
                        <UsersIcon class="h-5 w-5" />
                    </template>
                    Usuários
                </NavLink>

                <NavLink :href="route('councilors.index')" :active="route().current('councilors.index')">
                    <template #icon>
                        <BuildingOfficeIcon class="h-5 w-5" />
                    </template>
                    Vereadores
                </NavLink>

                <NavLink :href="route('timers.index')" :active="route().current('timers.index')">
                    <template #icon>
                        <ClockIcon class="h-5 w-5" />
                    </template>
                    Tempos
                </NavLink>

                <NavLink :href="route('document-categories.index')"
                    :active="route().current('document-categories.index')">
                    <template #icon>
                        <NewspaperIcon class="h-5 w-5" />
                    </template>
                    Categorias de Documentos
                </NavLink>

                <hr />

                <NavLink :href="route('logout')" method="post" as="button">
                    <template #icon>
                        <ArrowLeftEndOnRectangleIcon class="h-5 w-5 text-red-500" />
                    </template>
                    <span class="text-red-500">Sair</span>
                </NavLink>

                <div class="flex-grow"></div>

                <div class="space-y-1 mt-auto pb-4 text-center">
                    <NavLink href="#" @click.prevent="applyTheme('light')" :active="!isDark">
                        <SunIcon class="h-5 w-5 text-white" />
                    </NavLink>

                    <NavLink href="#" @click.prevent="applyTheme('dark')" :active="isDark">
                        <MoonIcon :class="['h-5 w-5', { 'text-white': isDark, 'text-gray-400': !isDark }]" />
                    </NavLink>
                </div>
            </nav>
        </aside>

        <main class="flex-1 overflow-auto p-6 ml-64 min-h-screen">
            <slot />
        </main>
    </div>
</template>