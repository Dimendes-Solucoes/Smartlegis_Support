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
});
</script>

<template>
    <BaseButtonLink :href="href" :as="as" :color="color" class="p-1 flex justify-center items-center">
        <slot></slot>
    </BaseButtonLink>
</template>