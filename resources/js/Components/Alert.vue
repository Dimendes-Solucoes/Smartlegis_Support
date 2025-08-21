<script setup lang="ts">
import { ref, watch } from 'vue';

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
        <div v-if="show"
            :class="['fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg w-full max-w-sm', alertClasses[props.type], 'transition-opacity duration-500']">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg v-if="props.type === 'success'" class="h-5 w-5 text-green-400"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <svg v-else class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
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

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>