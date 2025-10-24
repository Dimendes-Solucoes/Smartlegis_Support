<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';

defineProps<{
    form: ReturnType<typeof useForm>;
}>();
</script>

<template>
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="start_date" value="Data de Início" />
                <TextInput id="start_date" type="date" v-model="form.start_date" required class="mt-1 block w-full" />
                <InputError :message="form.errors.start_date" class="mt-2" />
            </div>
            <div>
                <InputLabel for="end_date" value="Data de Fim (opcional)" />
                <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" />
                <InputError :message="form.errors.end_date" class="mt-2" />
            </div>
        </div>
    </div>
</template>