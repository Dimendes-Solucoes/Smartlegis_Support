<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { SunIcon, MoonIcon, Bars3Icon } from '@heroicons/vue/24/solid';
import { staticNavigationLinks } from '@/data/navigationLinks';

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
});

</script>

<template>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen">
        <div 
            v-if="isSidebarOpen" 
            @click="toggleSidebar" 
            class="fixed inset-0 bg-black/50 z-30 lg:hidden"
        ></div>

        <aside
            :class="[
                'bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-screen top-0 left-0 z-40 transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'w-64' : 'w-14' 
            ]">
            <div class="flex items-center justify-center h-16 border-b border-gray-200 dark:border-gray-700 relative">
                <button 
                    @click="toggleSidebar"
                    class="absolute p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none top-1/2 -translate-y-1/2"
                    :class="isSidebarOpen ? 'left-1' : 'left-1/2 -translate-x-1/2'"
                    aria-label="Toggle sidebar"
                >
                    <Bars3Icon class="h-6 w-6" />
                </button>
                
                <Link :href="route('tenant.settings')" class="flex items-center">
                    <span class="text-md font-semibold text-gray-800 dark:text-gray-200" v-show="isSidebarOpen">
                        {{ currentTenantCityName }}
                    </span>
                </Link>
            </div>

            <nav class="flex-1 px-2 py-6 space-y-1 flex flex-col" v-show="isSidebarOpen">
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
                                <component :is="link.icon" :class="['h-5 w-5', link.iconClass]" />
                            </template>
                            <span :class="link.textClass">{{ link.label }}</span>
                        </NavLink>
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

        <main 
            :class="[
                'flex-1 overflow-auto p-4 sm:p-6 transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'lg:ml-64' : 'lg:ml-14' 
            ]">
            
            <slot />
        </main>
    </div>
</template>