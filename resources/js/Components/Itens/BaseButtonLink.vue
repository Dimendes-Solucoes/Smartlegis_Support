<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const dynamicColorClasses = computed(() => {
    const classes = [
        `bg-${props.color}-500`,
        `hover:bg-${props.color}-700`,
        `active:bg-${props.color}-900`,
        `focus:border-${props.color}-900`,
        `focus:ring-${props.color}-300`,
    ];

    if (props.color === 'yellow') {
        classes.push('text-black');
    } else {
        classes.push('text-white');
    }

    return classes.join(' ');
});

const baseButtonClasses = computed(() => `
    inline-flex items-center border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest
    focus:outline-none disabled:opacity-25 transition ease-in-out duration-150
    ${dynamicColorClasses.value}
`);
</script>

<template>
    <Link :href="href" :class="baseButtonClasses">
    <slot></slot>
    </Link>
</template>