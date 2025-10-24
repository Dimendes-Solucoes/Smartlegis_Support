<script setup lang="ts">
import InputError from "@/Components/Form/InputError.vue";
import InputLabel from "@/Components/Form/InputLabel.vue";
import TextInput from "@/Components/Form/TextInput.vue";
import TextareaInput from "@/Components/Form/TextareaInput.vue";
import MultiSelect from "@/Components/Form/MultiSelect.vue";
import FileAttachment from "@/Components/Form/FileAttachment.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";
import { computed } from "vue";

interface TicketType {
    id: number;
    title: string;
}

interface Tenant {
    id: string;
    city_name: string;
}

const props = defineProps<{
    form: any;
    ticket_types: TicketType[];
    tenants: Tenant[];
}>();

const tenantOptions = computed(() =>
    props.tenants.map((tenant) => ({
        value: tenant.id,
        label: tenant.city_name,
    }))
);
</script>

<template>
    <div class="space-y-6">
        <div>
            <InputLabel for="ticket_type_id" value="Tipo" />
            <SelectInput
                id="ticket_type_id"
                v-model="form.ticket_type_id"
                :options="ticket_types"
                placeholder="Selecione um tipo"
                value-key="id"
                label-key="title"
                required
            />
            <InputError :message="form.errors.ticket_type_id" class="mt-2" />
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
            v-model="form.tenant_ids"
            :options="tenantOptions"
            label="Cidades"
            placeholder="Selecione as cidades"
            :error="form.errors.tenant_ids"
            id="tenant_ids"
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
