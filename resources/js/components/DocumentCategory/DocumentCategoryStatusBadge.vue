<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: number;
}>();

const badgeClasses = computed(() => {
    const baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium';

    if (props.status === 0) {
        return `${baseClasses} bg-red-500 text-white dark:bg-red-500`;
    } else {
        return `${baseClasses} bg-green-500 text-white dark:bg-green-500 dark:text-black`;
    }
});

const badgeText = computed(() => {
    return props.status === 1 ? 'Ativo' : 'Inativo';
});

</script>

<template>
    <span :class="badgeClasses">
        {{ badgeText }}
    </span>
</template>