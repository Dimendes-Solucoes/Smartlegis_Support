<script setup lang="ts">
import { ref, watch } from 'vue';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps<{
    message: string | null;
    type: 'success' | 'error';
}>();

const show = ref(false);

watch(() => props.message, (newMessage) => {
    if (newMessage) {
        show.value = true;
        setTimeout(() => {
            show.value = false;
        }, 3000);
    }
}, { immediate: true });

const alertClasses = {
    success: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
    error: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
};
</script>

<template>
    <Transition name="fade">
        <div v-if="show" :class="['fixed z-50 p-4 rounded-md shadow-lg w-11/12 md:w-full md:max-w-sm', 'transition-opacity duration-500', alertClasses[props.type], {
            'top-4 right-1/2 transform translate-x-1/2 md:right-4 md:translate-x-0': true
        }]">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <CheckCircleIcon v-if="props.type === 'success'" class="h-5 w-5 text-green-400" />
                    <XCircleIcon v-else class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium">{{ props.message }}</p>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>