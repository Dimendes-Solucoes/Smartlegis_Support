<script setup lang="ts">
import PrimaryButton from "@/components/Common/PrimaryButton.vue";

interface User {
    id: number;
    name: string;
}

interface TicketType {
    id: number;
    title: string;
}

interface TicketStatus {
    id: number;
    title: string;
    color: string;
}

interface Credential {
    id: string;
    city_name: string;
}

interface Ticket {
    id: number;
    code: string;
    title: string;
    description: string;
    type: TicketType;
    status: TicketStatus;
    author: User;
    credentials: Credential[];
    created_at: string;
    updated_at: string;
}

defineProps<{
    ticket: Ticket;
}>();

const emit = defineEmits<{
    (e: "edit"): void;
}>();

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
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Informações</h2>
            <PrimaryButton @click="emit('edit')"> Editar Ticket </PrimaryButton>
        </div>

        <div class="space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-600">Título</p>
                <p class="text-lg text-gray-900 mt-1">{{ ticket.title }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600">Descrição</p>
                <p class="text-gray-900 mt-1 whitespace-pre-wrap">
                    {{ ticket.description }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Autor</p>
                    <p class="text-gray-900 mt-1">{{ ticket.author.name }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600">Última Atualização</p>
                    <p class="text-gray-900 mt-1">
                        {{ formatDate(ticket.updated_at) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tipo</p>
                    <p class="text-gray-900 mt-1">{{ ticket.type.title }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-600">Status</p>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border mt-1"
                    >
                        {{ ticket.status.title }}
                    </span>
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600">Cidades Envolvidas</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span
                        v-for="credential in ticket.credentials"
                        :key="credential.id"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800"
                    >
                        {{ credential.city_name }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
