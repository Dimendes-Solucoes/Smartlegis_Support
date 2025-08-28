<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import FeatureCard from '@/Components/Dashboard/FeatureCard.vue';
import TextButton from '@/Components/Itens/TextButton.vue';
import {
    ClipboardDocumentListIcon,
    BuildingOfficeIcon,
    UsersIcon,
    MegaphoneIcon,
    NewspaperIcon
} from '@heroicons/vue/24/solid';

const authUser = usePage().props.auth.user;
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                        Bem-vindo(a) ao Smartlegis, {{ authUser.name }}!
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Este é o seu painel de controle. A partir daqui, você pode gerenciar todas as atividades legislativas.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <FeatureCard title="Sessões" :icon="ClipboardDocumentListIcon">
                        <template #description>
                            <p>
                                Aqui você pode criar novas sessões, definir datas e gerenciar a pauta de documentos a serem discutidos e votados.
                            </p>
                        </template>
                        <template #actions>
                            <TextButton :href="route('sessions.index')">
                                Gerenciar Sessões
                            </TextButton>
                        </template>
                    </FeatureCard>

                    <FeatureCard title="Participantes" :icon="UsersIcon">
                        <template #description>
                            <p>
                                Gerencie os participantes do sistema, incluindo Vereadores e Usuários. Cadastre novos membros, edite perfis e controle o status (ativo/inativo).
                            </p>
                        </template>
                        <template #actions>
                            <div class="flex items-center space-x-2">
                                <TextButton :href="route('councilors.index')">
                                    Ver Vereadores
                                </TextButton>
                                <TextButton :href="route('users.index')">
                                    Ver Usuários
                                </TextButton>
                            </div>
                        </template>
                    </FeatureCard>

                    <FeatureCard title="Processo Legislativo" :icon="MegaphoneIcon">
                         <template #description>
                            <p>
                               Controle as etapas de fala e debate durante uma sessão. Gerencie os inscritos na Tribuna, nas Discussões e mais, a partir da tela de gerenciamento de uma sessão.
                            </p>
                        </template>
                        <template #actions>
                             <div class="flex items-center space-x-2">
                                <TextButton :href="route('sessions.index')">
                                    Acessar Sessões
                                </TextButton>
                            </div>
                        </template>
                    </FeatureCard>
                    
                    <FeatureCard title="Documentos" :icon="NewspaperIcon">
                         <template #description>
                            <p>
                                Organize todos os tipos de documentos legislativos. Crie e gerencie as Documentos e suas Categorias.
                            </p>
                        </template>
                        <template #actions>
                            <TextButton :href="route('document-categories.index')">
                                Gerenciar Categorias
                            </TextButton>
                        </template>
                    </FeatureCard>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>