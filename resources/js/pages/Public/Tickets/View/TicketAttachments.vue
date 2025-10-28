<script setup lang="ts">
import { ref } from "vue";
import Modal from "@/components/Common/Modal.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import FileAttachment from "@/components/Form/FileAttachment.vue";
import { PaperClipIcon, TrashIcon } from "@heroicons/vue/24/outline";

interface User {
    id: number;
    name: string;
}

interface TicketAttachment {
    id: number;
    user: User;
    file_name: string;
    file_path: string;
    file_url: string;
    created_at: string;
}

interface AttachmentForm {
    attachments: File[];
    errors: {
        attachments?: string;
    };
    processing: boolean;
}

defineProps<{
    attachments: TicketAttachment[];
    attachmentForm: AttachmentForm;
    currentUserId?: number;
}>();

const emit = defineEmits<{
    (e: "submit"): void;
    (e: "delete", attachmentId: number): void;
}>();

const showAttachmentModal = ref(false);

const canDeleteAttachment = (attachment: TicketAttachment, currentUserId?: number) => {
    return currentUserId === attachment.user.id;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const handleSubmit = () => {
    emit("submit");
    showAttachmentModal.value = false;
};
</script>

<template>
    <div
        class="bg-white dark:bg-gray-800 border dark:border-gray-500 rounded-lg shadow p-6"
    >
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Anexos</h2>
            <PrimaryButton @click="showAttachmentModal = true">
                Adicionar Anexos
            </PrimaryButton>
        </div>

        <div class="space-y-4">
            <div
                v-for="attachment in attachments"
                :key="attachment.id"
                class="border-l-4 border-green-500 bg-gray-50 dark:bg-gray-700 p-4 rounded-r-lg"
            >
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ attachment.user.name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ formatDate(attachment.created_at) }}
                        </p>
                    </div>
                    <button
                        v-if="canDeleteAttachment(attachment, currentUserId)"
                        @click="emit('delete', attachment.id)"
                        type="button"
                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                        title="Excluir anexo"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <PaperClipIcon class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                    <a
                        :href="attachment.file_url"
                        target="_blank"
                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline font-medium"
                    >
                        {{ attachment.file_name }}
                    </a>
                </div>
            </div>

            <div
                v-if="attachments.length === 0"
                class="text-center text-gray-500 dark:text-gray-400 py-8"
            >
                Nenhum anexo ainda.
            </div>
        </div>
    </div>

    <Modal
        :show="showAttachmentModal"
        @close="showAttachmentModal = false"
        max-width="2xl"
    >
        <div class="p-6 bg-white dark:bg-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
                Adicionar Anexos
            </h2>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <FileAttachment
                    v-model="attachmentForm.attachments"
                    label="Anexos"
                    :error="attachmentForm.errors.attachments"
                    id="ticket_attachments"
                    :max-size="20"
                    :max-files="10"
                />

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <SecondaryButton
                        @click="showAttachmentModal = false"
                        type="button"
                        :disabled="attachmentForm.processing"
                    >
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton
                        :class="{ 'opacity-25': attachmentForm.processing }"
                        :disabled="attachmentForm.processing"
                    >
                        Enviar Anexos
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
