<script setup lang="ts">
import AuthenticatedLayout from '@/layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import {
    ChevronUpIcon,
    ChevronDownIcon,
    FunnelIcon,
} from '@heroicons/vue/24/outline';

interface TenantReport {
    tenant_id: number;
    tenant_city: string;
    total_docs: number;
    total_signed: number;
    total_pending: number;
    total_expired: number;
}

const props = defineProps<{
    report: TenantReport[];
    filters: { start_date?: string; end_date?: string };
}>();

// ── Datas padrão (primeiro e último dia do mês atual) ──────────────────────

const today = new Date();
const firstOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
    .toISOString().split('T')[0];
const lastOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0)
    .toISOString().split('T')[0];

const startDate = ref(props.filters.start_date ?? firstOfMonth);
const endDate = ref(props.filters.end_date ?? lastOfMonth);

// Garante que o filtro padrão seja aplicado na primeira carga
onMounted(() => {
    if (!props.filters.start_date && !props.filters.end_date) {
        router.get(
            route('clicksign.report'),
            { start_date: firstOfMonth, end_date: lastOfMonth },
            { preserveState: true, replace: true }
        );
    }
});

// ── Filtros ────────────────────────────────────────────────────────────────

const applyFilters = () => {
    router.get(
        route('clicksign.report'),
        {
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const clearFilters = () => {
    startDate.value = firstOfMonth;
    endDate.value = lastOfMonth;
    router.get(
        route('clicksign.report'),
        { start_date: firstOfMonth, end_date: lastOfMonth },
        { preserveState: true, replace: true }
    );
};

const hasCustomFilters = computed(() =>
    startDate.value !== firstOfMonth ||
    endDate.value !== lastOfMonth
);

// ── Ordenação ──────────────────────────────────────────────────────────────

type SortKey = keyof TenantReport;
const sortKey = ref<SortKey>('total_docs');
const sortAsc = ref(false);

const setSort = (key: SortKey) => {
    if (sortKey.value === key) sortAsc.value = !sortAsc.value;
    else { sortKey.value = key; sortAsc.value = false; }
};

const sortedReport = computed(() =>
    [...props.report].sort((a, b) => {
        const vA = a[sortKey.value], vB = b[sortKey.value];
        if (typeof vA === 'string' && typeof vB === 'string')
            return sortAsc.value ? vA.localeCompare(vB) : vB.localeCompare(vA);
        return sortAsc.value ? (vA as number) - (vB as number) : (vB as number) - (vA as number);
    })
);

// ── Totais ─────────────────────────────────────────────────────────────────

const grand = computed(() => ({
    total_docs: props.report.reduce((s, r) => s + r.total_docs, 0),
    total_signed: props.report.reduce((s, r) => s + r.total_signed, 0),
    total_pending: props.report.reduce((s, r) => s + r.total_pending, 0),
    total_expired: props.report.reduce((s, r) => s + r.total_expired, 0),
}));

// ── Helpers ────────────────────────────────────────────────────────────────

const pct = (part: number, total: number) =>
    total === 0 ? '0%' : `${Math.round((part / total) * 100)}%`;

const rankColor = (i: number) => [
    'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'bg-gray-100  text-gray-700  dark:bg-gray-700      dark:text-gray-300',
    'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
][i] ?? '';

const SortIcon = (key: SortKey) =>
    sortKey.value === key ? (sortAsc.value ? ChevronUpIcon : ChevronDownIcon) : null;
</script>

<template>

    <Head title="Relatório ClickSign" />

    <AuthenticatedLayout>
        <div class="max-w-screen-xl mx-auto px-4 py-6 space-y-6">

            <!-- Filtros -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex flex-wrap items-end gap-4">
                    <FunnelIcon class="h-5 w-5 text-gray-400 self-center" />

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Data inicial
                        </label>
                        <input v-model="startDate" type="date"
                            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                                   text-sm text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            Data final
                        </label>
                        <input v-model="endDate" type="date"
                            class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700
                                   text-sm text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <button @click="applyFilters"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Filtrar
                    </button>

                    <button v-if="hasCustomFilters" @click="clearFilters"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                        Limpar
                    </button>
                </div>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="card in [
                    { label: 'Total Documentos', value: grand.total_docs, color: 'text-indigo-600 dark:text-indigo-400' },
                    { label: 'Assinados', value: grand.total_signed, color: 'text-green-600  dark:text-green-400' },
                    { label: 'Pendentes', value: grand.total_pending, color: 'text-yellow-600 dark:text-yellow-400' },
                    { label: 'Expirados', value: grand.total_expired, color: 'text-red-600    dark:text-red-400' },
                ]" :key="card.label"
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 text-center">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ card.label }}
                    </p>
                    <p class="mt-1 text-3xl font-bold" :class="card.color">
                        {{ card.value.toLocaleString('pt-BR') }}
                    </p>
                </div>
            </div>

            <!-- Tabela -->
            <div v-if="report.length > 0"
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 w-8"></th>

                                <th v-for="col in [
                                    { key: 'tenant_city', label: 'Cidade', align: 'left' },
                                    { key: 'total_docs', label: 'Documentos', align: 'center' },
                                    { key: 'total_signed', label: 'Assinados', align: 'center' },
                                    { key: 'total_pending', label: 'Pendentes', align: 'center' },
                                    { key: 'total_expired', label: 'Expirados', align: 'center' },
                                ]" :key="col.key" @click="setSort(col.key as SortKey)" class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider
                                           dark:text-gray-300 cursor-pointer hover:text-gray-700
                                           dark:hover:text-gray-100 select-none"
                                    :class="col.align === 'center' ? 'text-center' : 'text-left'">
                                    <span class="inline-flex items-center gap-1"
                                        :class="col.align === 'center' ? 'justify-center' : ''">
                                        {{ col.label }}
                                        <component :is="SortIcon(col.key as SortKey)"
                                            v-if="SortIcon(col.key as SortKey)" class="h-3 w-3" />
                                    </span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="(row, i) in sortedReport" :key="row.tenant_id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span v-if="i < 3"
                                        :class="['inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold', rankColor(i)]">
                                        {{ i + 1 }}
                                    </span>
                                    <span v-else class="text-xs text-gray-400 pl-1">{{ i + 1 }}</span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                    {{ row.tenant_city }}
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ row.total_docs.toLocaleString('pt-BR') }}
                                    </span>
                                    <div class="mt-1 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1">
                                        <div class="bg-indigo-500 h-1 rounded-full transition-all"
                                            :style="{ width: pct(row.total_docs, sortedReport[0]?.total_docs) }" />
                                    </div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        {{ row.total_signed.toLocaleString('pt-BR') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                        {{ row.total_pending.toLocaleString('pt-BR') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                        {{ row.total_expired.toLocaleString('pt-BR') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot class="bg-gray-50 dark:bg-gray-700 border-t-2 border-gray-300 dark:border-gray-600
                                      font-semibold text-gray-900 dark:text-white">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-xs uppercase tracking-wider text-gray-500">
                                    Total geral
                                </td>
                                <td class="px-4 py-3 text-center text-indigo-600 dark:text-indigo-400">
                                    {{ grand.total_docs.toLocaleString('pt-BR') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ grand.total_signed.toLocaleString('pt-BR') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ grand.total_pending.toLocaleString('pt-BR') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ grand.total_expired.toLocaleString('pt-BR') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div v-else
                class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-500 dark:text-gray-400">
                    Nenhum uso de ClickSign encontrado no período selecionado.
                </p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>