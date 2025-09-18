<script setup lang="ts">
import TextButton from '@/Components/Itens/TextButton.vue';

defineProps<{
    title: string;
    buttonLabel?: string;
    buttonRoute?: string;
}>();
</script>

<template>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
            {{ title }}
        </h1>
        <TextButton v-if="buttonRoute && buttonLabel" :href="route(buttonRoute)">
            {{ buttonLabel }}
        </TextButton>
    </div>
</template>