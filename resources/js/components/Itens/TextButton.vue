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
        validator: (value) => ['indigo', 'red', 'yellow', 'gray', 'orange'].includes(value),
    },
});
</script>

<template>
    <BaseButtonLink :href="href" :color="color" class="px-4 py-2">
        <slot></slot>
    </BaseButtonLink>
</template>