<script setup lang="ts">
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
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
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Anexos</h2>

        <div class="space-y-4 mb-6">
            <div
                v-for="attachment in attachments"
                :key="attachment.id"
                class="border-l-4 border-green-500 bg-gray-50 p-4 rounded-r-lg"
            >
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ attachment.user.name }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ formatDate(attachment.created_at) }}
                        </p>
                    </div>
                    <button
                        v-if="canDeleteAttachment(attachment, currentUserId)"
                        @click="emit('delete', attachment.id)"
                        type="button"
                        class="text-red-600 hover:text-red-800 transition-colors"
                        title="Excluir anexo"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>
                <div class="flex items-center space-x-3">
                    <PaperClipIcon class="w-5 h-5 text-gray-600" />
                    <a
                        :href="attachment.file_url"
                        target="_blank"
                        class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                    >
                        {{ attachment.file_name }}
                    </a>
                </div>
            </div>

            <div v-if="attachments.length === 0" class="text-center text-gray-500 py-8">
                Nenhum anexo ainda.
            </div>
        </div>

        <form @submit.prevent="emit('submit')" class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Adicionar Anexos</h3>

            <div class="space-y-4">
                <FileAttachment
                    v-model="attachmentForm.attachments"
                    label="Anexos"
                    :error="attachmentForm.errors.attachments"
                    id="ticket_attachments"
                    :max-size="20"
                    :max-files="10"
                />

                <div class="flex items-center justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': attachmentForm.processing }"
                        :disabled="attachmentForm.processing"
                    >
                        Enviar Anexos
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
