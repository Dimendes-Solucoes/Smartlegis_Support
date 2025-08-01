<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

interface Tenant {
    id: string;
    name: string;
    city: string;
}

const props = defineProps<{
    tenants: Tenant[];
}>();

const form = useForm({
    tenant_id: "",
});

const submit = () => {
    form.post(route('tenant.change'), {
        forceFormData: true
    });
};
</script>

<template>

    <Head title="Configurações" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p>Selecione a cidade que deseja editar</p>

                        <form @submit.prevent="submit">
                            <select name="tenant_id" id="tenant-select" v-model="form.tenant_id"
                                class="mt-3 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                                <option value="" selected disabled>Selecione</option>
                                <option v-for="tenant in props.tenants" :key="tenant.id" :value="tenant.id">
                                    {{ tenant.city ?? tenant.name }}
                                </option>
                            </select>

                            <div v-if="form.errors.tenant_id" class="text-red-500 text-sm mt-1">
                                {{ form.errors.tenant_id }}
                            </div>

                            <button type="submit" :disabled="form.processing || !form.tenant_id"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Confirmar Seleção
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>