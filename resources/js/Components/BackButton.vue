<script setup lang="ts">
import { ChevronLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <a :href="href" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 transition-colors">
        <ChevronLeftIcon class="h-5 w-5 mr-1" />
        <slot>Voltar</slot>
    </a>
</template>
