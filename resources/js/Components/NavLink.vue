<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    href: string;
    active?: boolean;
    method?: string;
    as?: string;
}>();

const classes = computed(() => {
    const baseClasses = 'inline-flex items-center px-4 py-2 text-sm font-medium leading-5 rounded-md transition duration-150 ease-in-out';

    if (props.active) {
        return `${baseClasses} bg-blue-700 text-white focus:outline-none focus:bg-blue-700`;
    } else {
        return `${baseClasses} text-gray-700 hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:bg-gray-200 focus:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:bg-gray-700 dark:focus:text-white`;
    }
});
</script>

<template>
    <Link :href="href" :class="classes" :method="method" :as="as">
    <span v-if="$slots.icon" class="mr-2">
        <slot name="icon" />
    </span>
    <slot />
    </Link>
</template>