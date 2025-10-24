<script setup lang="ts">
defineProps<{
    modelValue: string;
}>();

defineEmits<{
    "update:modelValue": [value: string];
}>();
</script>

<template>
    <textarea
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
    />
</template>
