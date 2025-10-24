<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import BackButtonRow from "@/Components/BackButtonRow.vue";
import TicketForm from "./TicketForm.vue";

interface TicketType {
    id: number;
    name: string;
}

interface Tenant {
    id: string;
    city_name: string;
}

const props = defineProps<{
    formData: {
        ticket_types: TicketType[];
        tenants: Tenant[];
    };
}>();

const form = useForm({
    title: "",
    description: "",
    ticket_type_id: "",
    attachments: [] as File[],
    tenant_ids: [] as string[],
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
                :tenants="props.formData.tenants"
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
