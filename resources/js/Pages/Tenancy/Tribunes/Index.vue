<script setup lang="ts">
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { UsersIcon, ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/24/solid';
import IconButton from '@/Components/Itens/IconButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { debounce } from 'lodash';


interface Tribune {
    id: number;
    type: string; 
    session_name: string;
}

interface PaginatedTribunes {
    data: Tribune[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const props = defineProps<{
    tribunes: PaginatedTribunes;
    filters: {
        sort: string;
        direction: string;
        search: string;
    }
}>();

const sortBy = (field: string) => {
    let direction = 'asc';
    if (props.filters.sort === field && props.filters.direction === 'asc') {
        direction = 'desc';
    }

    router.get(route('tribunes.index'), {
        sort: field,
        direction: direction,
        search: props.filters.search, 
    }, {
        preserveState: true,
        replace: true,
    });
};

const search = ref(props.filters.search);
watch(search, debounce((value: string) => {
    router.get(route('tribunes.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300)); 

//tipo de tribuna
const getTribuneType = (type: string): string => {
    const types: { [key: string]: string } = {
        '1': 'Pequeno Expediente',
        '2': 'Grande Expediente',
        '3': 'Ordem do Dia',
    };
    return types[type] || 'Tipo Desconhecido';
};

</script>

<template>
    <Head title="Tribunas" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-4">
                            <TextInput 
                                type="text"
                                v-model="search"
                                placeholder="Buscar por nome da sessão..."
                                class="w-full md:w-1/2"
                            />
                        </div>

                        <div v-if="props.tribunes.data.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            <button @click="sortBy('session_name')" class="flex items-center space-x-1">
                                                <span>Sessão Vinculada</span>
                                                </button>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                             <button @click="sortBy('type')" class="flex items-center space-x-1">
                                                <span>Tipo de Tribuna</span>
                                                <ChevronUpIcon v-if="filters.sort === 'type' && filters.direction === 'asc'" class="h-4 w-4" />
                                                <ChevronDownIcon v-if="filters.sort === 'type' && filters.direction === 'desc'" class="h-4 w-4" />
                                            </button>
                                        </th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    <tr v-for="tribune in props.tribunes.data" :key="tribune.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ tribune.session_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ getTribuneType(tribune.type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <IconButton :href="route('tribunes.edit', tribune.id)" color="indigo" title="Ver Inscritos">
                                                <UsersIcon class="h-5 w-5" />
                                            </IconButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <p>Nenhuma tribuna encontrada.</p>
                        </div>

                        <div v-if="props.tribunes.data.length > 0 && props.tribunes.links.length > 3" class="mt-6 flex justify-center">
                            <Link
                                v-for="(link, index) in props.tribunes.links"
                                :key="index"
                                :href="link.url || ''"
                                class="px-4 py-2 text-sm"
                                :class="{
                                    'bg-indigo-500 text-white rounded-md': link.active,
                                    'text-gray-500 hover:text-gray-800': !link.active,
                                    'cursor-not-allowed text-gray-400': !link.url
                                }"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>