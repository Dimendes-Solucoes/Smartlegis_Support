<script setup lang="ts">
import IconButton from '@/Components/Itens/IconButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { Head } from '@inertiajs/vue3';

interface Timer {
    id: number;
    title: string;
    locale: string;
    timer: string;
}

const props = defineProps<{
    timers: Timer[];
}>();

const formatTimer = (timeString: string) => {
    if (!timeString || typeof timeString !== 'string') {
        return '';
    }

    const parts = timeString.split(':');
    if (parts.length !== 3) {
        return '';
    }

    const hours = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);
    const seconds = parseInt(parts[2], 10);

    const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

    const displayMinutes = Math.floor(totalSeconds / 60);
    const displaySeconds = totalSeconds % 60;

    let formattedParts = [];
    if (displayMinutes > 0) {
        formattedParts.push(`${displayMinutes} min`);
    }
    if (displaySeconds > 0 || (displayMinutes === 0 && displaySeconds === 0)) {
        formattedParts.push(`${displaySeconds} seg`);
    }

    return formattedParts.join(' e ');
};
</script>

<template>

    <Head title="Tempos" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="props.timers.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Título</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Referência</th>
                                        <th scope="col"
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Tempo</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="timer in props.timers" :key="timer.id">
                                        <td class="px-4 py-4 whitespace-nowrap">{{ timer.title }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ timer.locale || '-' }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">{{ formatTimer(timer.timer) }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                                            <IconButton :href="route('timers.edit', timer.id)" color="yellow"
                                                title="Editar">
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>