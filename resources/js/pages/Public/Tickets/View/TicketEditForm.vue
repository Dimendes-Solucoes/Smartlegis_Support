<script setup lang="ts">
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import MultiSelect from "@/components/Form/MultiSelect.vue";
import { computed } from "vue";

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

interface UpdateForm {
    ticket_type_id: number;
    ticket_status_id: number;
    credential_ids: string[];
    errors: {
        ticket_type_id?: string;
        ticket_status_id?: string;
        credential_ids?: string;
    };
    processing: boolean;
}

interface FormData {
    ticket_types: TicketType[];
    ticket_status: TicketStatus[];
    credentials: Credential[];
}

const props = defineProps<{
    updateForm: UpdateForm;
    formData: FormData;
}>();

const emit = defineEmits<{
    (e: "submit"): void;
    (e: "cancel"): void;
}>();

const credentialOptions = computed(() =>
    props.formData.credentials.map((credential) => ({
        value: credential.id,
        label: credential.city_name,
    }))
);
</script>

<template>
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Editar Ticket</h2>

        <form @submit.prevent="emit('submit')" class="space-y-4">
            <SelectInput
                v-model="updateForm.ticket_type_id"
                label="Tipo"
                :error="updateForm.errors.ticket_type_id"
                id="ticket_type_id"
                required
            >
                <option value="">Selecione um tipo</option>
                <option
                    v-for="type in formData.ticket_types"
                    :key="type.id"
                    :value="type.id"
                >
                    {{ type.title }}
                </option>
            </SelectInput>

            <SelectInput
                v-model="updateForm.ticket_status_id"
                label="Status"
                :error="updateForm.errors.ticket_status_id"
                id="ticket_status_id"
                required
            >
                <option value="">Selecione um status</option>
                <option
                    v-for="status in formData.ticket_status"
                    :key="status.id"
                    :value="status.id"
                >
                    {{ status.title }}
                </option>
            </SelectInput>

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
                    @click="emit('cancel')"
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
    </div>
</template>
