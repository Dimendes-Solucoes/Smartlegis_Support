<script setup>
import { computed } from 'vue';

const props = defineProps({
    link: {
        type: String,
        required: true,
        default: '#'
    },
    color: {
        type: String,
        default: 'indigo'
    },
    icon: {
        type: [Object, String],
        required: true
    },
    title: {
        type: String,
        default: ''
    }
});

const baseClasses = 'flex items-center justify-center p-1 rounded-md border-2 flex-shrink-0';

const colorClasses = computed(() => {
    return `bg-${props.color}-600 hover:bg-${props.color}-700 border-${props.color}-600`;
});
</script>

<template>
    <a :href="link" target="_blank" rel="noopener noreferrer" :class="[baseClasses, colorClasses]" :title="title">
        <component :is="icon" class="h-5 w-5 text-white" />
    </a>
</template>