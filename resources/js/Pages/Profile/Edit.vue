<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="mx-auto space-y-4">
            <div class="bg-white lg:p-4 p-2 border sm:rounded-lg dark:bg-gray-800">
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="max-w-xl" />
            </div>

            <div class="bg-white lg:p-4 p-2 border sm:rounded-lg dark:bg-gray-800">
                <UpdatePasswordForm class="max-w-xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
