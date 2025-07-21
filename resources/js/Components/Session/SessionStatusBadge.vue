<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium whitespace-nowrap';

    switch (props.status) {
        case 1: 
            return `${baseClasses} bg-blue-500 text-white dark:bg-blue-600`;
        case 2: 
            //animate-pulse' para dar um efeito de "ao vivo"
            return `${baseClasses} bg-green-500 text-white dark:bg-green-600 animate-pulse`;
        case 3: 
            return `${baseClasses} bg-gray-500 text-white dark:bg-gray-700`;
        default:
            return `${baseClasses} bg-yellow-500 text-white dark:bg-yellow-600`;
    }
});

const badgeText = computed(() => {
    switch (props.status) {
        case 1:
            return 'Aguardando Votação';
        case 2:
            return 'Em Votação';
        case 3:
            return 'Finalizada';
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