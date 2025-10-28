<script setup lang="ts">
import InputError from "@/components/Form/InputError.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import TextInput from "@/components/Form/TextInput.vue";
import TextareaInput from "@/components/Form/TextareaInput.vue";
import MultiSelect from "@/components/Form/MultiSelect.vue";
import FileAttachment from "@/components/Form/FileAttachment.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
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

const props = defineProps<{
    form: any;
    ticket_types: TicketType[];
    ticket_status: TicketStatus[];
    credentials: Credential[];
}>();

const credentialOptions = computed(() =>
    props.credentials.map((credential) => ({
        value: credential.id,
        label: credential.city_name,
    }))
);
</script>

<template>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:space-x-4">
            <div class="md:flex-1 mb-4 md:mb-0">
                <InputLabel for="ticket_type_id" value="Tipo" />
                <SelectInput
                    id="ticket_type_id"
                    v-model="form.ticket_type_id"
                    :options="ticket_types"
                    placeholder="Selecione um tipo"
                    value-key="id"
                    label-key="title"
                    required
                    :disablePlaceholder="true"
                />
                <InputError :message="form.errors.ticket_type_id" class="mt-2" />
            </div>

            <div class="md:flex-1">
                <InputLabel for="ticket_status_id" value="Status" />
                <SelectInput
                    id="ticket_status_id"
                    v-model="form.ticket_status_id"
                    :options="ticket_status"
                    placeholder="Selecione um status"
                    value-key="id"
                    label-key="title"
                    required
                    :disablePlaceholder="true"
                />
                <InputError :message="form.errors.ticket_status_id" class="mt-2" />
            </div>
        </div>

        <div>
            <InputLabel for="title" value="Título" />
            <TextInput
                id="title"
                v-model="form.title"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
            />
            <InputError :message="form.errors.title" class="mt-2" />
        </div>

        <div>
            <InputLabel for="description" value="Descrição" />
            <TextareaInput
                id="description"
                v-model="form.description"
                class="mt-1 block w-full"
                rows="5"
                required
            />
            <InputError :message="form.errors.description" class="mt-2" />
        </div>

        <MultiSelect
            v-model="form.credential_ids"
            :options="credentialOptions"
            label="Cidades"
            placeholder="Selecione as cidades"
            :error="form.errors.credential_ids"
            id="credential_ids"
        />

        <FileAttachment
            v-model="form.attachments"
            label="Anexos"
            :error="form.errors.attachments"
            id="attachments"
            :max-size="20"
            :max-files="10"
        />
    </div>
</template>
