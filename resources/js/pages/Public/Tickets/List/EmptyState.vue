<script setup lang="ts">
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";

defineProps<{
    hasActiveFilters: boolean;
}>();

const emit = defineEmits<{
    clearFilters: [];
}>();
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
        <div class="max-w-md mx-auto">
            <div
                class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4"
            >
                <MagnifyingGlassIcon class="h-8 w-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Nenhum ticket encontrado
            </h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                {{
                    hasActiveFilters
                        ? "Tente ajustar os filtros de busca"
                        : "Comece criando seu primeiro ticket"
                }}
            </p>
            <button
                v-if="hasActiveFilters"
                @click="emit('clearFilters')"
                class="text-blue-600 dark:text-blue-400 hover:underline"
            >
                Limpar filtros
            </button>
        </div>
    </div>
</template>
