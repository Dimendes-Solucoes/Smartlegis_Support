<script setup lang="ts">
const props = defineProps<{
    icon: any;
    title: string;
}>();
</script>

<template>
    <div class="bg-white dark:bg-gray-800 dark:border dark:border-gray-700 rounded-lg shadow p-6 flex flex-col justify-between">
        <div class="flex items-center">
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                <component :is="icon" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="ml-4 text-xl font-semibold text-gray-800 dark:text-gray-200">{{ title }}</h3>
        </div>
        <div class="mt-4 text-gray-600 dark:text-gray-400 text-sm space-y-2">
            <slot name="description" />
        </div>
        <div class="mt-5 border-t border-gray-200 dark:border-gray-700 pt-4">
            <slot name="actions" />
        </div>
    </div>
</template>