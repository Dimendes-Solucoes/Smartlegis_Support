<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, nextTick } from 'vue';
import { applyTimerMask } from '@/Utils/timers';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import BackButtonRow from '@/Components/BackButtonRow.vue';

interface Timer {
    id: number;
    title: string;
    timer: string;
}

const props = defineProps<{
    timer: Timer;
}>();

const form = useForm({
    id: props.timer.id,
    title: props.timer.title,
    timer: props.timer.timer,
});

const timerInputRef = ref<HTMLInputElement | null>(null);

watch(() => form.timer, (newValue, oldValue = '') => {
    const cursorPosition = timerInputRef.value?.selectionStart ?? 0;
    const oldLength = oldValue.length;

    const maskedValue = applyTimerMask(newValue);

    form.timer = maskedValue;

    nextTick(() => {
        if (timerInputRef.value) {
            let newCursorPosition = cursorPosition;

            const colonCountOld = (oldValue.match(/:/g) || []).length;
            const colonCountNew = (maskedValue.match(/:/g) || []).length;

            if (maskedValue.length > oldLength) {
                if (colonCountNew > colonCountOld) {
                    newCursorPosition += (colonCountNew - colonCountOld);
                }
            } else if (maskedValue.length < oldLength) {
                if (colonCountNew < colonCountOld) {
                    newCursorPosition -= (colonCountOld - colonCountNew);
                }
            }

            if (newCursorPosition > maskedValue.length) {
                newCursorPosition = maskedValue.length;
            }

            timerInputRef.value.setSelectionRange(newCursorPosition, newCursorPosition);
        }
    });
}, { immediate: true });

const submit = () => {
    form.put(route('timers.update', props.timer.id));
};
</script>

<template>

    <Head :title="`Editar Tempo`" />

    <AuthenticatedLayout>
        <BackButtonRow :href="route('timers.index')" />

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div>
                    <InputLabel for="title" value="Título" />
                    <TextInput id="title" type="text" class="mt-1 block w-full" v-model="form.title" required />
                    <InputError class="mt-2" :message="form.errors.title" />
                </div>

                <div>
                    <InputLabel for="timer" value="Tempo (HH:MM:SS)" />
                    <TextInput id="timer" type="text" class="mt-1 block w-full" v-model="form.timer"
                        placeholder="00:00:00" required autofocus maxlength="8" ref="timerInputRef" />
                    <InputError class="mt-2" :message="form.errors.timer" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Salvar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>