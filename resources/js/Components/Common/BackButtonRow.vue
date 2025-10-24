<script setup lang="ts">
import BackButton from './BackButton.vue';

const props = defineProps<{
    href: string;
}>();
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-2">
            <BackButton :href="props.href" />
        </div>
    </div>
</template>
