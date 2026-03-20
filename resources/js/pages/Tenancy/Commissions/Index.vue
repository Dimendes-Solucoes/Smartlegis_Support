<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import TextButton from '@/components/Itens/TextButton.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import { Head, router } from '@inertiajs/vue3';
import IconButton from '@/components/Itens/IconButton.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';

interface Commission {
    id: number;
    comission_name: string;
    type_description: string;
    legislature?: { id: number; title: string };
}

interface Legislature {
    id: number;
    title: string;
    is_current: boolean;
}

const props = defineProps<{
    commissions: Commission[];
    legislatures: Legislature[];
    filters: { legislature_id?: number };
}>();

const selectedLegislature = ref<number | null>(props.filters.legislature_id ?? null);

watch(selectedLegislature, (value) => {
    router.get(route('commissions.index'), { legislature_id: value ?? undefined }, {
        preserveState: true,
        replace: true,
    });
});
</script>

<template>

    <Head title="Comissões" />

    <AuthenticatedLayout>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4">
            <div class="w-full sm:w-72">
                <InputLabel value="Filtrar por Legislatura" />
                <SelectInput v-model="selectedLegislature" :options="props.legislatures" value-key="id"
                    label-key="title" placeholder="Todas as legislaturas" class="mt-1 w-full" />
            </div>

            <TextButton :href="route('commissions.create')">
                Nova Comissão
            </TextButton>
        </div>

        <div v-if="props.commissions.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Comissão
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Tipo
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Legislatura
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="commission in props.commissions" :key="commission.id">
                        <td class="px-4 py-4 whitespace-normal">{{ commission.comission_name }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ commission.type_description }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ commission.legislature?.title ?? '—' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <IconButton :href="route('commissions.edit', commission.id)" color="yellow" title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p>Nenhuma comissão encontrada.</p>
        </div>
    </AuthenticatedLayout>
</template>