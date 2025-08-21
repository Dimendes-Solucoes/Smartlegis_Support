<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { SunIcon, MoonIcon, Bars3Icon, ChevronDownIcon } from '@heroicons/vue/24/solid';
import { staticNavigationLinks } from '@/data/navigationLinks';
import Alert from '@/Components/Alert.vue';

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

const filteredNavigationLinks = computed(() => {
    return staticNavigationLinks.filter(link => {
        if (link.type === 'separator') {
            return true;
        }
        if (link.route === 'admin.index') {
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

const expandedGroups = ref<string[]>([]);

const isGroupActive = (children: any[]) => {
    return children.some((child) => route().current(child.route));
};

const isGroupExpanded = (link: any) => {
    if (link.children) {
        return isGroupActive(link.children) || expandedGroups.value.includes(link.label!);
    }
    return false;
};

const toggleGroup = (label: string) => {
    const index = expandedGroups.value.indexOf(label);
    if (index > -1) {
        expandedGroups.value.splice(index, 1);
    } else {
        expandedGroups.value.push(label);
    }
};


onMounted(() => {
    const mediaQuery = window.matchMedia('(min-width: 1024px)');
    const handleMediaQueryChange = (e: MediaQueryListEvent) => {
        if (!e.matches && isSidebarOpen.value) {
            isSidebarOpen.value = false;
            localStorage.setItem("isSidebarOpen", "false");
        }
    };

    mediaQuery.addEventListener('change', handleMediaQueryChange);
    handleMediaQueryChange(mediaQuery as any);

    // Pré-expande o grupo se a rota atual pertence a ele
    const currentRouteName = route().current();
    if (currentRouteName) {
        filteredNavigationLinks.value.forEach(link => {
            if (link.type === 'group' && link.children) {
                if (link.children.some(child => route().current(child.route as string))) {
                    if (!expandedGroups.value.includes(link.label!)) {
                        expandedGroups.value.push(link.label!);
                    }
                }
            }
        });
    }
});

const mainContentClasses = computed(() => {
    return {
        'lg:ml-64': isSidebarOpen.value,
        'ml-0': !isSidebarOpen.value,
        'transition-all duration-300 ease-in-out': true
    };
});
</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen flex">
        <div v-if="isSidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

        <aside v-if="isSidebarOpen"
            class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-screen top-0 left-0 z-40 w-64 transition-all duration-300 ease-in-out">
            <div
                class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 relative flex-shrink-0">
                <Link :href="route('tenant.settings')" class="flex items-center" v-show="isSidebarOpen">
                <span class="text-md font-semibold text-gray-800 dark:text-gray-200">
                    {{ currentTenantCityName }}
                </span>
                </Link>
            </div>

            <nav class="flex-1 px-2 py-1 space-y-1 flex flex-col overflow-y-auto" v-show="isSidebarOpen">
                <template v-for="(link, index) in filteredNavigationLinks" :key="index">
                    <template v-if="link.type === 'separator'">
                        <hr class="my-2 border-gray-200 dark:border-gray-700" />
                    </template>
                    <template v-else-if="link.type === 'link'">
                        <NavLink :href="route(link.route!)" :active="route().current(link.route!)"
                            :method="link.method || 'get'" :as="link.as || 'a'">
                            <template #icon>
                                <component :is="link.icon" :class="['h-5 w-5', link.iconClass]" />
                            </template>
                            <span :class="link.textClass">{{ link.label }}</span>
                        </NavLink>
                    </template>
                    <template v-else-if="link.type === 'group'">
                        <button @click="toggleGroup(link.label!)" :class="[
                            'inline-flex items-center w-full text-left px-4 py-2 text-sm font-medium leading-5 rounded-md transition duration-150 ease-in-out',
                            { 'bg-blue-700 text-white focus:outline-none focus:bg-blue-700': isGroupExpanded(link) },
                            { 'text-gray-700 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:bg-gray-200 focus:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 dark:focus:text-white': !isGroupExpanded(link) }
                        ]">
                            <component :is="link.icon" :class="['h-5 w-5 mr-2']" />
                            <span class="flex-1">{{ link.label }}</span>
                            <ChevronDownIcon
                                :class="['h-4 w-4 transform transition-transform duration-200', { 'rotate-180': isGroupExpanded(link) }]" />
                        </button>

                        <div v-if="isGroupExpanded(link)" class="pl-6 space-y-1">
                            <NavLink v-for="(child, childIndex) in link.children" :key="childIndex"
                                :href="route(child.route!)" :active="route().current(child.route!)"
                                :method="child.method || 'get'" :as="child.as || 'a'">
                                <template #icon>
                                    <component :is="child.icon" :class="['h-5 w-5', child.iconClass]" />
                                </template>
                                <span :class="child.textClass">{{ child.label }}</span>
                            </NavLink>
                        </div>
                    </template>
                </template>

                <div class="flex-grow"></div>

                <div class="space-y-1 mt-auto pb-4">
                    <div class="flex justify-center">
                        <NavLink href="#" @click.prevent="applyTheme('light')" :active="!isDark">
                            <SunIcon class="h-5 w-5 text-white" />
                        </NavLink>

                        <NavLink href="#" @click.prevent="applyTheme('dark')" :active="isDark">
                            <MoonIcon :class="['h-5 w-5', { 'text-white': isDark, 'text-gray-400': !isDark }]" />
                        </NavLink>
                    </div>
                </div>
            </nav>
        </aside>

        <div class="fixed top-3 left-1 z-50">
            <button @click="toggleSidebar" class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                aria-label="Toggle sidebar">
                <Bars3Icon class="h-6 w-6" />
            </button>
        </div>

        <main class="flex-1 overflow-auto p-4" :class="mainContentClasses">
            <slot />
        </main>
    </div>

    <Alert :message="page.props.flash.success ?? null" type="success" />
    <Alert :message="page.props.flash.error ?? null" type="error" />
</template>