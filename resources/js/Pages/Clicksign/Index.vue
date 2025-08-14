<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/solid';
import ConfirmDeletionModal from '@/Components/ConfirmDeletionModal.vue';
import { ref } from 'vue';

interface Clicksign {
    tenant_id: number;
    tenant_city: string;
    quantity: string;
}

const props = defineProps<{
    clicksigns: Clicksign[];
}>();

const cityToDelete = ref<Clicksign | null>(null);
const confirmingCityDeletion = ref(false);

const form = useForm({});

const clearCity = () => {
    if (!cityToDelete.value) return;

    form.delete(route('clicksign.destroy', {
        tenant_id: cityToDelete.value.tenant_id
    }), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const openConfirmDeleteModal = (clicksign: Clicksign) => {
    cityToDelete.value = clicksign;
    confirmingCityDeletion.value = true;
};

const closeModal = () => {
    confirmingCityDeletion.value = false;
    cityToDelete.value = null;
    form.reset();
}
</script>

<template>
    <Head title="Clicksign" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="clicksigns.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Cidade</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Quantidade</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="clicksign in clicksigns" :key="clicksign.tenant_id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ clicksign.tenant_city }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ clicksign.quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton @click="openConfirmDeleteModal(clicksign)" color="red"
                                                title="Limpar" class="ml-2" :disabled="form.processing">
                                                <div v-if="form.processing && cityToDelete?.tenant_id === clicksign.tenant_id"
                                                    class="flex items-center">
                                                    <svg class="animate-spin h-5 w-5 text-white"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <TrashIcon v-else class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhum evento clicksign encontrado.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingCityDeletion" title="Limpar Registros"
        :message="`Tem certeza que deseja limpar os registros de eventos Clicksign de '${cityToDelete?.tenant_city}'? Essa ação não pode ser desfeita.`"
        @close="closeModal" @confirm="clearCity" />
</template>