<script setup lang="ts">
import IconButton from '@/Components/Itens/IconButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { Head } from '@inertiajs/vue3';
import { formatTimer } from '@/Utils/timers';

interface Timer {
    id: number;
    title: string;
    locale: string;
    timer: string;
}

const props = defineProps<{
    timers: Timer[];
}>();

</script>

<template>

    <Head title="Tempos" />

    <AuthenticatedLayout>
        <div v-if="props.timers.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Título</th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Tempo</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    <tr v-for="timer in props.timers" :key="timer.id">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span>{{ timer.title }}</span>
                                <span class="text-gray-500 dark:text-gray-300 text-sm">
                                    {{ timer.locale || '-' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ formatTimer(timer.timer) }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                            <IconButton :href="route('timers.edit', timer.id)" color="yellow" title="Editar">
                                <PencilSquareIcon class="h-5 w-5" />
                            </IconButton>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else>
            <p>Nenhum tempo encontrado para esta cidade.</p>
        </div>
    </AuthenticatedLayout>
</template>