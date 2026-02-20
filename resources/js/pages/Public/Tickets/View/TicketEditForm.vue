<script setup lang="ts">
import Modal from "@/components/Common/Modal.vue";
import PrimaryButton from "@/components/Common/PrimaryButton.vue";
import SecondaryButton from "@/components/Common/SecondaryButton.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import MultiSelect from "@/components/Form/MultiSelect.vue";
import { computed } from "vue";
import { Credential, TicketStatus, TicketType } from "@/types/ticket";

interface UpdateForm {
    ticket_type_id: number;
    status: string;
    credential_ids: string[];
    errors: {
        ticket_type_id?: string;
        status?: string;
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
    show: boolean;
    updateForm: UpdateForm;
    formData: FormData;
}>();

const emit = defineEmits<{
    (e: "submit"): void;
    (e: "close"): void;
}>();

const credentialOptions = computed(() =>
    props.formData.credentials.map((credential) => ({
        value: credential.id,
        label: credential.city_name,
    }))
);
</script>

<template>
    <Modal :show="show" @close="emit('close')" max-width="2xl">
        <div class="p-6 bg-white dark:bg-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
                Editar Ticket
            </h2>

            <form @submit.prevent="emit('submit')" class="space-y-4">
                <div>
                    <SelectInput
                        v-model="updateForm.ticket_type_id"
                        label="Tipo"
                        :options="formData.ticket_types"
                        :error="updateForm.errors.ticket_type_id"
                        id="ticket_type_id"
                        required
                    />
                </div>

                <div>
                    <SelectInput
                        v-model="updateForm.status"
                        label="Status"
                        :options="formData.ticket_status"
                        :error="updateForm.errors.status"
                        id="status"
                        required
                    />
                </div>

                <div>
                    <MultiSelect
                        v-model="updateForm.credential_ids"
                        :options="credentialOptions"
                        label="Cidades"
                        placeholder="Selecione as cidades"
                        :error="updateForm.errors.credential_ids"
                        id="credential_ids"
                    />
                </div>

                <div class="flex items-center justify-end space-x-3 mt-6">
                    <SecondaryButton
                        @click="emit('close')"
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
    </Modal>
</template>
