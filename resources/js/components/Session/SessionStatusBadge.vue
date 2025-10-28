<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();


const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';

    switch (props.status) {
        case 1: 
            return `${baseClasses} bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200`;
        case 2: 
            return `${baseClasses} bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 animate-pulse`;
        case 3: 
            return `${baseClasses} bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200`;
        default: //vai que 
            return `${baseClasses} bg-gray-100 text-gray-800`;
    }
});

const badgeText = computed(() => {
    switch (props.status) {
        case 1:
            return 'Aguardando';
        case 2:
            return 'Em Votação';
        case 3:
            return 'Concluída';
        default:
            return 'Desconhecido';
    }
});

</script>

<template>
    <span :class="badgeClasses">
        {{ badgeText }}
    </span>
</template>