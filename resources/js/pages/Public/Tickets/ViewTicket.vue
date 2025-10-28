<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import InputError from "@/components/Form/InputError.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextareaInput from "@/components/Form/TextareaInput.vue";
import MultiSelect from "@/components/Form/MultiSelect.vue";
import FileAttachment from "@/components/Form/FileAttachment.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import { computed, ref } from "vue";

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

interface User {
    id: number;
    name: string;
}

interface TicketAttachement {
    id: number;
    user: User;
    file_path: string;
    file_name: string;
    created_at: string;
}

interface TicketMessage {
    id: number;
    content: string;
    author: User;
    created_at: string;
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
    messages: TicketMessage[];
    attachments: TicketAttachement[];
}

const props = defineProps<{
    ticket: Ticket;
    formData: {
        ticket_types: TicketType[];
        ticket_status: TicketStatus[];
        credentials: Credential[];
    };
}>();

const editMode = ref(false);

// Form para atualizar o ticket
const updateForm = useForm({
    ticket_type_id: props.ticket.type.id,
    ticket_status_id: props.ticket.status.id,
    credential_ids: props.ticket.credentials.map((t) => t.id),
});

// Form para adicionar mensagens
const messageForm = useForm({
    content: "",
});

// Form para adicionar anexos
const attachmentForm = useForm({
    attachments: [] as File[],
});

const credentialOptions = computed(() =>
    props.formData.credentials.map((credential) => ({
        value: credential.id,
        label: credential.city_name,
    }))
);

const submitUpdate = () => {
    updateForm.put(route("tickets.update", props.ticket.code), {
        onSuccess: () => {
            editMode.value = false;
        },
    });
};

const submitMessage = () => {
    messageForm.post(route("tickets.messages.store", props.ticket.code), {
        onSuccess: () => {
            messageForm.reset();
        },
    });
};

const submitAttachment = () => {
    attachmentForm.post(route("tickets.attachments.store", props.ticket.code), {
        onSuccess: () => {
            attachmentForm.reset();
        },
    });
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
    <Head :title="`Ticket #${ticket.code}`" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('tickets.index')" />

        <div class="mt-6 space-y-6">
            <!-- Código do Ticket em Destaque -->
            <div class="bg-gray-200 rounded-lg shadow-lg p-6 text-dark">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium opacity-90">Código do Ticket</p>
                        <h1 class="text-2xl font-bold mt-1">#{{ ticket.code }}</h1>
                    </div>
                    <div class="text-right">
                        <p class="text-sm opacity-90">Criado em</p>
                        <p class="text-lg font-semibold">
                            {{ formatDate(ticket.created_at) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Informações Originais do Ticket (Não Editáveis) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações</h2>

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
                            <p class="text-sm font-medium text-gray-600">
                                Última Atualização
                            </p>
                            <p class="text-gray-900 mt-1">
                                {{ formatDate(ticket.updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção Editável (Tipo, Status, Cidades) -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Encaminhamento</h2>
                    <SecondaryButton
                        v-if="!editMode"
                        @click="editMode = true"
                        type="button"
                    >
                        Editar
                    </SecondaryButton>
                </div>

                <form v-if="editMode" @submit.prevent="submitUpdate" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="ticket_type_id" value="Tipo" />
                            <SelectInput
                                id="ticket_type_id"
                                v-model="updateForm.ticket_type_id"
                                :options="formData.ticket_types"
                                placeholder="Selecione um tipo"
                                value-key="id"
                                label-key="title"
                                required
                                :disablePlaceholder="true"
                            />
                            <InputError
                                :message="updateForm.errors.ticket_type_id"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="ticket_status_id" value="Status" />
                            <SelectInput
                                id="ticket_status_id"
                                v-model="updateForm.ticket_status_id"
                                :options="formData.ticket_status"
                                placeholder="Selecione um status"
                                value-key="id"
                                label-key="title"
                                required
                                :disablePlaceholder="true"
                            />
                            <InputError
                                :message="updateForm.errors.ticket_status_id"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <MultiSelect
                        v-model="updateForm.credential_ids"
                        :options="credentialOptions"
                        label="Cidades"
                        placeholder="Selecione as cidades"
                        :error="updateForm.errors.credential_ids"
                        id="credential_ids"
                    />

                    <div class="flex items-center justify-end space-x-3">
                        <SecondaryButton
                            @click="editMode = false"
                            type="button"
                            :disabled="updateForm.processing"
                        >
                            Cancelar
                        </SecondaryButton>
                        <PrimaryButton
                            :class="{ 'opacity-25': updateForm.processing }"
                            :disabled="updateForm.processing"
                        >
                            Salvar Alterações
                        </PrimaryButton>
                    </div>
                </form>

                <div v-else class="space-y-4">
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
                        <p class="text-sm font-medium text-gray-600">
                            Cidades Envolvidas
                        </p>
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

            <!-- Anexos -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Anexos</h2>

                <div class="space-y-4 mb-6">
                    <!-- Lista de anexos existentes -->
                    <div
                        v-for="attachment in ticket.attachments"
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
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg
                                class="w-5 h-5 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"
                                />
                            </svg>
                            <a
                                :href="attachment.file_path"
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 hover:underline font-medium"
                            >
                                {{ attachment.file_name }}
                            </a>
                        </div>
                    </div>

                    <div
                        v-if="ticket.attachments.length === 0"
                        class="text-center text-gray-500 py-8"
                    >
                        Nenhum anexo ainda.
                    </div>
                </div>

                <!-- Formulário para adicionar anexos -->
                <form @submit.prevent="submitAttachment" class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Adicionar Anexos
                    </h3>

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

            <!-- Histórico de Mensagens -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Mensagens</h2>

                <div class="space-y-4 mb-6">
                    <div
                        v-for="message in ticket.messages"
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
                        </div>
                        <p class="text-gray-800 whitespace-pre-wrap">
                            {{ message.content }}
                        </p>
                    </div>

                    <div
                        v-if="ticket.messages.length === 0"
                        class="text-center text-gray-500 py-8"
                    >
                        Nenhuma mensagem ainda. Seja o primeiro a enviar!
                    </div>
                </div>

                <!-- Formulário de Nova Mensagem -->
                <form @submit.prevent="submitMessage" class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Adicionar Mensagem
                    </h3>

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
                            <InputError
                                :message="messageForm.errors.content"
                                class="mt-2"
                            />
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
        </div>
    </AuthenticatedLayout>
</template>
