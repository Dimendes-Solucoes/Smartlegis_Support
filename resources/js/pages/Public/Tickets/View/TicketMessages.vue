<script setup lang="ts">
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextareaInput from "@/components/Form/TextareaInput.vue";
import InputError from "@/components/Form/InputError.vue";
import { TrashIcon } from "@heroicons/vue/24/outline";

interface User {
    id: number;
    name: string;
}

interface TicketMessage {
    id: number;
    content: string;
    author: User;
    created_at: string;
}

interface MessageForm {
    content: string;
    errors: {
        content?: string;
    };
    processing: boolean;
}

defineProps<{
    messages: TicketMessage[];
    messageForm: MessageForm;
    currentUserId?: number;
}>();

const emit = defineEmits<{
    (e: "submit"): void;
    (e: "delete", messageId: number): void;
}>();

const canDeleteMessage = (message: TicketMessage, currentUserId?: number) => {
    return currentUserId === message.author.id;
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
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Mensagens</h2>

        <div class="space-y-4 mb-6">
            <div
                v-for="message in messages"
                :key="message.id"
                class="border-l-4 border-blue-500 bg-gray-50 p-4 rounded-r-lg"
            >
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="font-semibold text-gray-900">
                            {{ message.author.name }}
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ formatDate(message.created_at) }}
                        </p>
                    </div>
                    <button
                        v-if="canDeleteMessage(message, currentUserId)"
                        @click="emit('delete', message.id)"
                        type="button"
                        class="text-red-600 hover:text-red-800 transition-colors"
                        title="Excluir mensagem"
                    >
                        <TrashIcon class="w-5 h-5" />
                    </button>
                </div>
                <p class="text-gray-800 whitespace-pre-wrap">
                    {{ message.content }}
                </p>
            </div>

            <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
                Nenhuma mensagem ainda. Seja o primeiro a enviar!
            </div>
        </div>

        <form @submit.prevent="emit('submit')" class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Adicionar Mensagem</h3>

            <div class="space-y-4">
                <div>
                    <InputLabel for="content" value="Mensagem" />
                    <TextareaInput
                        id="content"
                        v-model="messageForm.content"
                        class="mt-1 block w-full"
                        rows="4"
                        placeholder="Digite sua mensagem..."
                        required
                    />
                    <InputError :message="messageForm.errors.content" class="mt-2" />
                </div>

                <div class="flex items-center justify-end">
                    <PrimaryButton
                        :class="{ 'opacity-25': messageForm.processing }"
                        :disabled="messageForm.processing"
                    >
                        Enviar Mensagem
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </div>
</template>
