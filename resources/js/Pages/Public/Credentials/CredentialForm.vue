<script setup lang="ts">
import { computed } from "vue";
import InputError from "@/Components/Form/InputError.vue";
import InputLabel from "@/Components/Form/InputLabel.vue";
import TextInput from "@/Components/Form/TextInput.vue";
import { usePage } from "@inertiajs/vue3";
import ImageUploadWithCropper from "@/Components/User/ImageUploadWithCropper.vue";
import SelectInput from "@/Components/Form/SelectInput.vue";

interface Tenant {
    id: string;
    city_name: string;
}

const props = defineProps<{
    form: any;
    tenants?: Tenant[];
    isEditing?: boolean;
}>();

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <div class="space-y-4">
        <div v-if="!isEditing">
            <InputLabel for="tenant_id" value="Cidade (Tenant)" />
            <SelectInput
                v-model="form.tenant_id"
                :options="tenants"
                value-key="id"
                label-key="city_name"
                placeholder="Selecione um tenant disponível"
                :disable-placeholder="true"
                required
            />
            <InputError class="mt-2" :message="form.errors.tenant_id" />
        </div>

        <div>
            <InputLabel for="city_name" value="Nome da Cidade (para exibição)" />
            <TextInput
                id="city_name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.city_name"
                required
            />
            <InputError class="mt-2" :message="form.errors.city_name" />
        </div>

        <div>
            <InputLabel for="short_name" value="Nome Curto" />
            <TextInput
                id="short_name"
                type="text"
                class="mt-1 block w-full"
                v-model="form.short_name"
                required
            />
            <InputError class="mt-2" :message="form.errors.short_name" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="channel" value="Canal" />
                <TextInput
                    id="channel"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.channel"
                    required
                />
                <InputError class="mt-2" :message="form.errors.channel" />
            </div>
            <div>
                <InputLabel for="host" value="Host" />
                <TextInput
                    id="host"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.host"
                    required
                />
                <InputError class="mt-2" :message="form.errors.host" />
            </div>
        </div>

        <div v-if="user.is_root || !isEditing">
            <InputLabel
                for="key"
                :value="
                    isEditing ? 'Nova Chave (deixe em branco para não alterar)' : 'Chave'
                "
            />
            <TextInput
                id="key"
                type="text"
                class="mt-1 block w-full"
                v-model="form.key"
                :required="!isEditing"
            />
            <InputError class="mt-2" :message="form.errors.key" />
        </div>

        <div>
            <InputLabel for="city_shield" value="Brasão da Cidade" />
            <ImageUploadWithCropper
                v-model="form.city_shield"
                :error="form.errors.city_shield"
                :initial-image-url="form.existing_city_shield"
                class="mt-1"
            />
        </div>
    </div>
</template>
