<script setup lang="ts">
import { watch } from 'vue';
import InputError from "@/components/Form/InputError.vue";
import InputLabel from "@/components/Form/InputLabel.vue";
import Checkbox from "@/components/Form/Checkbox.vue";
import SelectInput from "@/components/Form/SelectInput.vue";
import TextInput from "@/components/Form/TextInput.vue";
import TextareaInput from "@/components/Form/TextareaInput.vue";

interface Party {
    id: number;
    name_party: string;
}

interface Mandate {
    id: number;
    title: string;
    start_at: string;
    end_at: string;
    is_current: boolean;
}

const props = defineProps<{
    form: any;
    parties: Party[];
    mandates?: Mandate[];
    isCreating?: boolean;
}>();

const showMandateSelect = () =>
    props.isCreating && (props.mandates?.length ?? 0) > 1;

watch(
    () => props.form.mandate_id as string | number,
    (newVal) => {
        const mandate = props.mandates?.find((m) => m.id == newVal);
        if (mandate) {
            props.form.is_vereador_active = mandate.is_current;
        }
    }
);

watch(
    () => props.mandates,
    (mandates) => {
        if (!props.isCreating) return;
        if (mandates?.length === 1) {
            props.form.mandate_id = mandates[0].id;
            props.form.is_vereador_active = mandates[0].is_current;
        }
    },
    { immediate: true }
);
</script>

<template>
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
        <p v-if="!props.isCreating" class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Informações legislativas
        </p>

        <div class="md:flex md:space-x-4 mb-4">
            <div class="flex-1 mb-4 md:mb-0">
                <InputLabel for="birthdate" value="Data de Nascimento" />
                <TextInput id="birthdate" type="date" class="mt-1 block w-full" v-model="form.birthdate" />
                <InputError class="mt-2" :message="form.errors.birthdate" />
            </div>
        </div>

        <div class="mb-4">
            <InputLabel for="summary" value="Resumo" />
            <TextareaInput id="summary" v-model="form.summary" class="mt-1 block w-full" rows="5"
                placeholder="Escreva um breve resumo sobre o vereador..." />
            <InputError class="mt-2" :message="form.errors.summary" />
        </div>

        <div class="md:flex md:space-x-4 mb-4">
            <div v-if="isCreating && showMandateSelect()" class="flex-1 mb-4 md:mb-0">
                <InputLabel for="mandate_id" value="Mandato" />
                <SelectInput
                    id="mandate_id"
                    v-model="form.mandate_id"
                    :options="mandates ?? []"
                    value-key="id"
                    label-key="title"
                    placeholder="Selecione um mandato"
                />
                <InputError class="mt-2" :message="form.errors.mandate_id" />
            </div>

            <div class="flex-1 mb-4 md:mb-0">
                <InputLabel for="party_id" value="Partido" />
                <SelectInput id="party_id" v-model="form.party_id" :options="parties" value-key="id"
                    label-key="name_party" placeholder="Selecione um partido" />
                <InputError class="mt-2" :message="form.errors.party_id" />
            </div>
        </div>

        <div class="mb-4 space-y-2">
            <label v-if="isCreating" class="flex items-center">
                <Checkbox v-model:checked="form.is_vereador_active" name="is_vereador_active" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Vereador ativo</span>
            </label>

            <label class="flex items-center">
                <Checkbox v-model:checked="form.is_leader" name="is_leader" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Vereador líder</span>
            </label>

            <label class="flex items-center">
                <Checkbox v-model:checked="form.is_first_secretary" name="is_first_secretary" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">1º Secretário</span>
            </label>
            <InputError class="mt-2" :message="form.errors.is_first_secretary" />
        </div>
    </div>
</template>
