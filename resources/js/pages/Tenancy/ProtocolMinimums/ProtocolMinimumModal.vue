<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue';
import ProtocolMinimumForm from './ProtocolMinimumForm.vue';
import PrimaryButton from '@/components/Common/PrimaryButton.vue';
import SecondaryButton from '@/components/Common/SecondaryButton.vue';
import { computed } from 'vue';

interface Category {
    id: number;
    name: string;
}

const props = defineProps<{
    show: boolean;
    form: any;
    categories: Category[];
}>();

const emit = defineEmits(['close', 'submit']);

const isEditing = computed(() => !!props.form.id);
</script>

<template>
    <Modal :show="show" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-200">
                {{ isEditing ? 'Editar Protocolo Mínimo' : 'Adicionar Protocolo Mínimo' }}
            </h2>

            <form @submit.prevent="$emit('submit')" class="mt-6 space-y-6">
                <ProtocolMinimumForm :form="form" :categories="categories" :is-editing="isEditing" />

                <div class="flex justify-end space-x-2">
                    <SecondaryButton type="button" @click="$emit('close')">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton :disabled="form.processing">
                        {{ isEditing ? 'Salvar Alterações' : 'Adicionar' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>