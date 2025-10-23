<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import IconButton from '@/Components/Itens/IconButton.vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
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

const clearCity = () => {
    if (!cityToDelete.value) return;

    router.delete(route('clicksign.destroy', {
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
}
</script>

<template>

    <Head title="Clicksign" />

    <AuthenticatedLayout>

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
                            <IconButton @click="openConfirmDeleteModal(clicksign)" color="red" title="Limpar"
                                class="ml-2">
                                <TrashIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>Nenhum evento clicksign encontrado.</p>
        </div>
    </AuthenticatedLayout>

    <ConfirmDeletionModal :show="confirmingCityDeletion" title="Limpar eventos clicksign"
        :message="`Tem certeza que deseja limpar os registros de eventos clicksign de '${cityToDelete?.tenant_city}'?`"
        @close="closeModal" @confirm="clearCity" />
</template>