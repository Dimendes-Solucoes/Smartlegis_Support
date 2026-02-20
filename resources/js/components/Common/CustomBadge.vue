<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
    title: string;
    color: string;
}>();

const badgeClasses = computed(() => {
    const baseClasses =
        "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium tracking-wider";
    return baseClasses;
});

const badgeStyle = computed(() => {
    return {
        backgroundColor: props.color + "aa",
        color: "#FFFFFF",
    };
});
</script>

<template>
    <span :class="badgeClasses" :style="badgeStyle">
        {{ title }}
    </span>
</template>
