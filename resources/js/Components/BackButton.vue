<script setup lang="ts">
import { ChevronLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <a :href="props.href" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 transition-colors text-sm">
        <ChevronLeftIcon class="h-5 w-5 mr-1" />
        <span style="margin-top: 2px;">Voltar</span>
    </a>
</template>
