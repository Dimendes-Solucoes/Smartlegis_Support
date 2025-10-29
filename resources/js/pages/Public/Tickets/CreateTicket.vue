<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import BackButtonRow from "@/components/Common/BackButtonRow.vue";
import TicketForm from "./TicketForm.vue";

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

const props = defineProps<{
    formData: {
        ticket_types: TicketType[];
        status: TicketStatus[];
        credentials: Credential[];
    };
}>();

const form = useForm({
    title: "",
    description: "",
    ticket_type_id: "",
    status: "",
    attachments: [] as File[],
    credential_ids: [] as string[],
});

const submit = () => {
    form.post(route("tickets.store"));
};
</script>

<template>
    <Head title="Novo Ticket" />
    <AuthenticatedLayout>
        <BackButtonRow :href="route('tickets.index')" />

        <form @submit.prevent="submit" class="mt-6">
            <TicketForm
                :form="form"
                :ticket_types="props.formData.ticket_types"
                :status="props.formData.status"
                :credentials="props.formData.credentials"
            />

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Criar Ticket
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
