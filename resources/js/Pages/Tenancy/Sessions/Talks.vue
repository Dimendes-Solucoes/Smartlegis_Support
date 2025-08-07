<script setup lang="ts">
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    UserGroupIcon,
    MicrophoneIcon,
    ChatBubbleOvalLeftEllipsisIcon,
    MegaphoneIcon,
    QuestionMarkCircleIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/solid';

const props = defineProps<{
    session: {
        id: number;
        name: string;
    };
}>();

</script>

<template>

    <Head :title="`${props.session.name}`" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6">
                    {{ props.session.name }}
                </h1>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="space-y-4">
                        <Link :href="route('sessions.quorums', props.session.id)"
                            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                        <UserGroupIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Quóruns</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('sessions.tribunes', props.session.id)"
                            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                        <MicrophoneIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tribunas</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('sessions.list_discussions', props.session.id)"
                            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                        <ChatBubbleOvalLeftEllipsisIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Discussões</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('sessions.big_discussions', props.session.id)"
                            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                        <MegaphoneIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Explanações Pessoais</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </Link>

                        <Link :href="route('sessions.questions_orders', props.session.id)"
                            class="block w-full p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 ease-in-out">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-full">
                                        <QuestionMarkCircleIcon class="h-6 w-6 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Questões de Ordem</span>
                                </div>
                                <div class="text-gray-500 dark:text-gray-400">
                                    <ChevronRightIcon class="h-5 w-5" />
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>