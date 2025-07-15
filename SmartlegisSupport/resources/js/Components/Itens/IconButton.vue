<script setup>
import BaseButtonLink from './BaseButtonLink.vue';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    color: {
        type: String,
        default: 'indigo',
        validator: (value) => ['indigo', 'red', 'yellow'].includes(value),
    },
});
</script>

<template>
    <BaseButtonLink :href="href" :color="color" class="p-1 flex justify-center items-center">
        <slot></slot>
    </BaseButtonLink>
</template>