<script setup lang="ts">
import { Squares2X2Icon, TableCellsIcon } from "@heroicons/vue/24/outline";

type ViewMode = "cards" | "table";

defineProps<{
    modelValue: ViewMode;
}>();

const emit = defineEmits<{
    "update:modelValue": [value: ViewMode];
}>();
</script>

<template>
    <div
        class="flex border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden"
    >
        <button
            type="button"
            @click="emit('update:modelValue', 'cards')"
            :class="[
                'px-3 py-2 transition-colors',
                modelValue === 'cards'
                    ? 'bg-gray-500 text-white'
                    : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700',
            ]"
            title="Visualização em cards"
        >
            <Squares2X2Icon class="h-5 w-5" />
        </button>
        <button
            type="button"
            @click="emit('update:modelValue', 'table')"
            :class="[
                'px-3 py-2 border-l border-gray-300 dark:border-gray-600 transition-colors',
                modelValue === 'table'
                    ? 'bg-gray-500 text-white'
                    : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700',
            ]"
            title="Visualização em tabela"
        >
            <TableCellsIcon class="h-5 w-5" />
        </button>
    </div>
</template>
