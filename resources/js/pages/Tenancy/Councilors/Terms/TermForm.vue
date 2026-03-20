<script setup lang="ts">
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import InputError from '@/components/Form/InputError.vue';
import SelectInput from '@/components/Form/SelectInput.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Legislature {
    id: number;
    title: string;
    start_at: string;
    end_at: string | null;
    is_current: boolean;
}

const props = defineProps<{
    form: ReturnType<typeof useForm>;
    legislatures: Legislature[];
}>();

const selectedLegislature = computed({
    get: () => props.form.legislature_id as number | null,
    set: (val) => { props.form.legislature_id = val; },
});
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel for="legislature_id" value="Legislatura" />
            <SelectInput id="legislature_id" v-model="selectedLegislature" :options="legislatures" value-key="id"
                label-key="title" placeholder="Selecione uma legislatura" />
            <InputError :message="form.errors.legislature_id" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <InputLabel for="start_date" value="Data de Início" />
                <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" />
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