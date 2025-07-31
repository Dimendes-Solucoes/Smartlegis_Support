<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps<{
    show: boolean;
    title: string;
    message: string;
}>();

const emit = defineEmits(['close', 'confirm']);

</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ title }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ message }}
            </p>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="$emit('close')"> Cancelar </SecondaryButton>

                <DangerButton class="ml-3" @click="$emit('confirm')">
                    Excluir
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>