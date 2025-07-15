<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/solid';
import TextButton from '@/Components/Itens/TextButton.vue';

interface CalendarEvent {
    id: number;
    title: string;
    start: string;
    end: string | null;
    status_id: number;
    status_name: string;
    tenant_city: string;
}

interface CalendarData {
    year: number;
    month: number;
    monthName: string;
    events: CalendarEvent[];
}

interface EventStatusDetail {
    bg: string;
    text: string;
    label: string;
}

interface EventStatusColorsMap {
    [key: number]: EventStatusDetail;
}

const props = defineProps<{
    calendarData: CalendarData;
}>();

const initialYear = props.calendarData ? props.calendarData.year : new Date().getFullYear();
const initialMonth = props.calendarData ? props.calendarData.month : new Date().getMonth() + 1;

const currentYear = ref(initialYear);
const currentMonth = ref(initialMonth);

const goToPreviousMonth = () => {
    if (currentMonth.value === 1) {
        currentMonth.value = 12;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const goToNextMonth = () => {
    if (currentMonth.value === 12) {
        currentMonth.value = 1;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

watch([currentMonth, currentYear], ([newMonth, newYear]) => {
    router.visit(route('calendar.index', { year: newYear, month: newMonth }), {
        preserveScroll: true,
        preserveState: true,
    });
});

const daysInMonth = computed(() => {
    const date = new Date(currentYear.value, currentMonth.value - 1, 1);
    const numDays = new Date(currentYear.value, currentMonth.value, 0).getDate();
    const firstDayOfWeek = date.getDay();

    const days = [];
    for (let i = 0; i < firstDayOfWeek; i++) {
        days.push(null);
    }
    for (let i = 1; i <= numDays; i++) {
        days.push(i);
    }
    return days;
});

const getEventsForDay = (day: number | null): CalendarEvent[] => {
    if (!day || !props.calendarData || !props.calendarData.events) return [];

    const targetDate = new Date(currentYear.value, currentMonth.value - 1, day);
    return props.calendarData.events.filter(event => {
        const eventStartDate = new Date(event.start);
        return eventStartDate.toDateString() === targetDate.toDateString();
    });
};

const eventStatusColors: EventStatusColorsMap = {
    1: { bg: 'bg-red-100 dark:bg-red-900', text: 'text-red-800 dark:text-red-200', label: 'Aguardando' },
    2: { bg: 'bg-yellow-100 dark:bg-yellow-900', text: 'text-yellow-800 dark:text-yellow-200', label: 'Em Votação' },
    3: { bg: 'bg-green-100 dark:bg-green-900', text: 'text-green-800 dark:text-green-200', label: 'Concluída' },
};

const getEventClasses = (statusId: number) => {
    return eventStatusColors[statusId] || { bg: 'bg-gray-100 dark:bg-gray-700', text: 'text-gray-800 dark:text-gray-200' };
};
</script>

<template>

    <Head :title="`Calendário - ${props.calendarData?.monthName || 'Mês'} ${props.calendarData?.year || 'Ano'}`" />

    <AuthenticatedLayout>
        <template #header>
            Calendário de Sessões
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-4">
                                <TextButton @click="goToPreviousMonth" color="gray">
                                    <ChevronLeftIcon class="h-5 w-5" />
                                </TextButton>
                                <h3 class="text-2xl font-semibold capitalize">
                                    {{ props.calendarData?.monthName || 'Carregando...' }} {{ props.calendarData?.year
                                        || '' }}
                                </h3>
                                <TextButton @click="goToNextMonth" color="gray">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </TextButton>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-1 text-center font-bold text-gray-600 dark:text-gray-300 mb-2">
                            <div>Dom</div>
                            <div>Seg</div>
                            <div>Ter</div>
                            <div>Qua</div>
                            <div>Qui</div>
                            <div>Sex</div>
                            <div>Sáb</div>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <div v-for="(day, index) in daysInMonth" :key="index"
                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-2 min-h-[80px] flex flex-col"
                                :class="{ 'bg-gray-50 dark:bg-gray-700': day === null, 'bg-white dark:bg-gray-800': day !== null }">
                                <span v-if="day" class="font-bold text-lg mb-1"
                                    :class="{ 'text-blue-600 dark:text-blue-400': day === new Date().getDate() && currentMonth === new Date().getMonth() + 1 && currentYear === new Date().getFullYear() }">
                                    {{ day }}
                                </span>
                                <div v-if="day" class="flex flex-col space-y-1">
                                    <div v-for="event in getEventsForDay(day)" :key="event.id"
                                        class="text-xs rounded-md p-1 truncate"
                                        :class="getEventClasses(event.status_id).bg + ' ' + getEventClasses(event.status_id).text"
                                        :title="`${event.title}`">
                                        {{ event.tenant_city }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-lg mb-2">Legenda:</h4>
                            <div class="flex flex-wrap gap-4">
                                <div v-for="(status, id) in eventStatusColors" :key="id"
                                    class="flex items-center space-x-2">
                                    <span class="w-4 h-4 rounded-full"
                                        :class="status.bg.replace(/-\d{2,3}/g, '-500')"></span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ status.label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>