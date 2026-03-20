<script setup>
import BaseButtonLink from './BaseButtonLink.vue';

const props = defineProps({
    href: {
        type: String,
        required: false,
        default: null,
    },
    color: {
        type: String,
        default: 'indigo',
        validator: (value) => ['indigo', 'red', 'yellow', 'gray', 'orange'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <BaseButtonLink :href="href" :color="color" :disabled="disabled" class="px-4 py-2">
        <slot></slot>
    </BaseButtonLink>
</template>