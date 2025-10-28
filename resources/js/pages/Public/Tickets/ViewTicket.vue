<script setup lang="ts">
import { computed, ref } from "vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import ConfirmDeletionModal from "@/components/Common/ConfirmDeletionModal.vue";
import TicketHeader from "./View/TicketHeader.vue";
import TicketInformation from "./View/TicketInformation.vue";
import TicketEditForm from "./View/TicketEditForm.vue";
import TicketAttachments from "./View/TicketAttachments.vue";
import TicketMessages from "./View/TicketMessages.vue";

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
    file_name: string;
    file_path: string;
    file_url: string;
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

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id);

const editMode = ref(false);

const confirmingAttachmentDeletion = ref(false);
const confirmingMessageDeletion = ref(false);
const attachmentToDelete = ref<number | null>(null);
const messageToDelete = ref<number | null>(null);

const updateForm = useForm({
    ticket_type_id: props.ticket.type.id,
    ticket_status_id: props.ticket.status.id,
    credential_ids: props.ticket.credentials.map((t) => t.id),
});

const messageForm = useForm({
    content: "",
});

const attachmentForm = useForm({
    attachments: [] as File[],
});

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

const openConfirmDeleteAttachment = (attachmentId: number) => {
    attachmentToDelete.value = attachmentId;
    confirmingAttachmentDeletion.value = true;
};

const deleteAttachment = () => {
    if (!attachmentToDelete.value) return;

    useForm({}).delete(
        route("tickets.attachments.delete", {
            code: props.ticket.code,
            attachment_id: attachmentToDelete.value,
        }),
        {
            preserveScroll: true,
            onSuccess: () => closeAttachmentModal(),
        }
    );
};

const closeAttachmentModal = () => {
    confirmingAttachmentDeletion.value = false;
    attachmentToDelete.value = null;
};

const openConfirmDeleteMessage = (messageId: number) => {
    messageToDelete.value = messageId;
    confirmingMessageDeletion.value = true;
};

const deleteMessage = () => {
    if (!messageToDelete.value) return;

    useForm({}).delete(
        route("tickets.messages.delete", {
            code: props.ticket.code,
            message_id: messageToDelete.value,
        }),
        {
            preserveScroll: true,
            onSuccess: () => closeMessageModal(),
        }
    );
};

const closeMessageModal = () => {
    confirmingMessageDeletion.value = false;
    messageToDelete.value = null;
};
</script>

<template>
    <Head :title="`Ticket #${ticket.code}`" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('tickets.index')" />

        <div class="mt-6 space-y-6">
            <TicketHeader :ticket="ticket" />

            <TicketInformation :ticket="ticket" @edit="editMode = true" />

            <TicketAttachments
                :attachments="ticket.attachments"
                :attachment-form="attachmentForm"
                :current-user-id="currentUserId"
                @submit="submitAttachment"
                @delete="openConfirmDeleteAttachment"
            />

            <TicketMessages
                :messages="ticket.messages"
                :message-form="messageForm"
                :current-user-id="currentUserId"
                @submit="submitMessage"
                @delete="openConfirmDeleteMessage"
            />
        </div>
    </AuthenticatedLayout>

    <TicketEditForm
        :show="editMode"
        :update-form="updateForm"
        :form-data="formData"
        @submit="submitUpdate"
        @close="editMode = false"
    />

    <ConfirmDeletionModal
        :show="confirmingAttachmentDeletion"
        title="Excluir Anexo"
        message="Tem certeza que deseja excluir este anexo? Esta ação não pode ser desfeita."
        @close="closeAttachmentModal"
        @confirm="deleteAttachment"
    />

    <ConfirmDeletionModal
        :show="confirmingMessageDeletion"
        title="Excluir Mensagem"
        message="Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita."
        @close="closeMessageModal"
        @confirm="deleteMessage"
    />
</template>
