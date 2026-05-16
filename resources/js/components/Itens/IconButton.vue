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
        validator: (value) => ['indigo', 'red', 'yellow', 'blue', 'green', 'orange'].includes(value),
    },
    as: String,
    target: {
        type: String,
        default: null,
    },
    rel: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <BaseButtonLink :href="href" :as="as" :color="color" :target="target" :rel="rel"
        class="p-1 flex justify-center items-center">
        <slot></slot>
    </BaseButtonLink>
</template>