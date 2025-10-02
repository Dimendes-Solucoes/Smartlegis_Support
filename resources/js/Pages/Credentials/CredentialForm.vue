<script setup lang="ts">
import { computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { usePage } from '@inertiajs/vue3';
import ImageUploadWithCropper from '@/Components/User/ImageUploadWithCropper.vue';

interface Tenant {
    id: string;
    city_name: string;
}

const props = defineProps<{
    form: any; 
    tenants?: Tenant[];
    isEditing?: boolean;
}>();

const user = computed(() => usePage().props.auth.user as { is_root: boolean });
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel for="tenant_id" value="Cidade (Tenant)" />
            <select v-if="!isEditing" v-model="form.tenant_id" required
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md shadow-sm">
                <option value="" disabled>Selecione um tenant disponível</option>
                <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">{{ tenant.city_name }}</option>
            </select>
            <TextInput v-else type="text" class="mt-1 block w-full bg-gray-100 dark:bg-gray-700"
                :model-value="form.tenant?.city_name || form.city_name" readonly />
            <InputError class="mt-2" :message="form.errors.tenant_id" />
        </div>

        <div>
            <InputLabel for="city_name" value="Nome da Cidade (para exibição)" />
            <TextInput id="city_name" type="text" class="mt-1 block w-full" v-model="form.city_name" required />
            <InputError class="mt-2" :message="form.errors.city_name" />
        </div>
        
        <div>
            <InputLabel for="short_name" value="Nome Curto" />
            <TextInput id="short_name" type="text" class="mt-1 block w-full" v-model="form.short_name" required />
            <InputError class="mt-2" :message="form.errors.short_name" />
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="channel" value="Canal" />
                <TextInput id="channel" type="text" class="mt-1 block w-full" v-model="form.channel" required />
                <InputError class="mt-2" :message="form.errors.channel" />
            </div>
            <div>
                <InputLabel for="host" value="Host" />
                <TextInput id="host" type="text" class="mt-1 block w-full" v-model="form.host" required />
                <InputError class="mt-2" :message="form.errors.host" />
            </div>
        </div>

        <div v-if="user.is_root">
            <InputLabel for="key" :value="isEditing ? 'Nova Chave (deixe em branco para não alterar)' : 'Chave'" />
            <TextInput id="key" type="password" class="mt-1 block w-full" v-model="form.key" :required="!isEditing"
                autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.key" />
        </div>
        
        <div>
            <InputLabel for="city_shield" value="Brasão da Cidade" />
            <ImageUploadWithCropper v-model="form.city_shield" :error="form.errors.city_shield"
                :initial-image-url="form.existing_city_shield" class="mt-1" />
        </div>
    </div>
</template>