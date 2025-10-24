<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";

interface Party {
    id: number;
    name_party: string;
}

const props = defineProps<{
    form: any;
    parties: Party[];
    isCreating?: boolean;
}>();
</script>

<template>
    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
        <p v-if="!props.isCreating" class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Informações legislativas
        </p>
        <div class="md:flex md:space-x-4 mb-4">
            <div class="flex-1 mb-4 md:mb-0">
                <InputLabel for="party_id" value="Partido" />
                <SelectInput
                    id="party_id"
                    v-model="form.party_id"
                    :options="parties"
                    value-key="id"
                    label-key="name_party"
                    placeholder="Selecione um partido"
                />
                <InputError class="mt-2" :message="form.errors.party_id" />
            </div>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <Checkbox v-model:checked="form.is_leader" name="is_leader" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                    >Vereador líder</span
                >
            </label>

            <label class="flex items-center mt-1">
                <Checkbox
                    v-model:checked="form.is_first_secretary"
                    name="is_first_secretary"
                />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400"
                    >1º Secretário</span
                >
            </label>
            <InputError class="mt-2" :message="form.errors.is_first_secretary" />
        </div>
    </div>
</template>
