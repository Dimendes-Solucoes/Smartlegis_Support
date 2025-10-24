<script setup lang="ts">
import { computed, ref, onMounted } from "vue";
import NavLink from "@/Components/Common/NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { SunIcon, MoonIcon, Bars3Icon } from "@heroicons/vue/24/outline";
import { staticNavigationLinks } from "@/data/navigationLinks";
import Alert from "@/Components/Common/Alert.vue";

interface AuthUser {
    id: number;
    name: string;
    email: string;
    is_root: boolean;
}

const isDark = ref(false);
const page = usePage();

const authUser = computed<AuthUser | null>(() => page.props.auth.user as AuthUser | null);
const isRootUser = computed(() => authUser.value && authUser.value.is_root);

const currentTenantCityName = computed(() => {
    return (page.props.tenant as any)?.city_name || "Selecione uma cidade";
});

function applyTheme(theme: string) {
    if (theme === "dark") {
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

const filteredNavigationLinks = computed(() => {
    return staticNavigationLinks.filter((link) => {
        if (link.type === "separator") {
            return true;
        }
        if (link.route === "admin.index") {
            return isRootUser.value;
        }
        if (link.route === "clicksign.index") {
            return isRootUser.value;
        }
        return true;
    });
});

const isSidebarOpen = ref(localStorage.getItem("isSidebarOpen") === "true");

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
    localStorage.setItem("isSidebarOpen", isSidebarOpen.value.toString());
};

onMounted(() => {
    const mediaQuery = window.matchMedia("(min-width: 1024px)");
    const handleMediaQueryChange = (e: MediaQueryListEvent) => {
        if (!e.matches && isSidebarOpen.value) {
            isSidebarOpen.value = false;
            localStorage.setItem("isSidebarOpen", "false");
        }
    };

    mediaQuery.addEventListener("change", handleMediaQueryChange);
    handleMediaQueryChange(mediaQuery as any);
});

const mainContentClasses = computed(() => {
    return {
        "lg:ml-64": isSidebarOpen.value,
        "ml-0": !isSidebarOpen.value,
        "transition-all duration-300 ease-in-out": true,
    };
});
</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen flex">
        <div
            v-if="isSidebarOpen"
            @click="toggleSidebar"
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"
        ></div>

        <aside
            v-if="isSidebarOpen"
            class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-screen top-0 left-0 z-40 w-64 transition-all duration-300 ease-in-out"
        >
            <div
                class="flex items-center justify-center h-14 border-b border-gray-200 dark:border-gray-700 relative flex-shrink-0"
            >
                <Link
                    :href="route('tenant.settings')"
                    class="flex items-center"
                    v-show="isSidebarOpen"
                >
                    <span class="text-md font-semibold text-gray-800 dark:text-gray-200">
                        {{ currentTenantCityName }}
                    </span>
                </Link>
            </div>

            <nav
                class="flex-1 px-2 py-1 space-y-1 flex flex-col overflow-y-auto"
                v-show="isSidebarOpen"
            >
                <template v-for="(link, index) in filteredNavigationLinks" :key="index">
                    <template v-if="link.type === 'separator'">
                        <hr class="my-2 border-gray-200 dark:border-gray-700" />
                    </template>
                    <template v-else-if="link.type === 'link'">
                        <NavLink
                            :href="route(link.route!)"
                            :active="route().current(link.route!)"
                            :method="link.method || 'get'"
                            :as="link.as || 'a'"
                        >
                            <template #icon>
                                <component
                                    :is="link.icon"
                                    :class="['h-5 w-5', link.iconClass]"
                                />
                            </template>
                            <span :class="link.textClass">{{ link.label }}</span>
                        </NavLink>
                    </template>
                </template>

                <div class="flex-grow"></div>

                <div class="space-y-1 mt-auto pb-4">
                    <div class="flex justify-center">
                        <NavLink
                            href="#"
                            @click.prevent="applyTheme('light')"
                            :active="!isDark"
                        >
                            <SunIcon class="h-5 w-5 text-white" />
                        </NavLink>

                        <NavLink
                            href="#"
                            @click.prevent="applyTheme('dark')"
                            :active="isDark"
                        >
                            <MoonIcon
                                :class="[
                                    'h-5 w-5',
                                    { 'text-white': isDark, 'text-gray-400': !isDark },
                                ]"
                            />
                        </NavLink>
                    </div>
                </div>
            </nav>
        </aside>

        <div class="fixed top-2 left-2 z-50">
            <button
                @click="toggleSidebar"
                class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none border bg-white dark:bg-gray-800 dark:border-gray-800 shadow-md"
                aria-label="Toggle sidebar"
            >
                <Bars3Icon class="h-5 w-5 dark:text-gray-100" />
            </button>
        </div>

        <main class="flex-1 overflow-auto p-2" :class="mainContentClasses">
            <div class="py-12">
                <div class="mx-auto">
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                    >
                        <div class="px-2 py-3 md:p-4 text-gray-900 dark:text-gray-100">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <Alert :flash-message="page.props.flash ?? null" />
</template>
