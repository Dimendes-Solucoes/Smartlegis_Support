<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import TermForm from './TermForm.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed } from 'vue';

const props = defineProps<{
    show: boolean;
    form: any; 
}>();

const emit = defineEmits(['close', 'submit']);

const isEditing = computed(() => !!props.form.id);

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                {{ isEditing ? 'Editar Mandato' : 'Adicionar Novo Mandato' }}
            </h2>

            <form @submit.prevent="$emit('submit')" class="mt-6 space-y-6">
                <TermForm :form="form" />

                <div class="flex justify-end space-x-2">
                    <SecondaryButton @click="closeModal" type="button"> Cancelar </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ isEditing ? 'Salvar Alterações' : 'Adicionar Mandato' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>