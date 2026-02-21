<script setup lang="ts">
import InputError from '@/components/Form/InputError.vue';
import InputLabel from '@/components/Form/InputLabel.vue';
import TextInput from '@/components/Form/TextInput.vue';
import ImageUploadWithCropper from '@/components/User/ImageUploadWithCropper.vue';

const props = defineProps<{
    form: any;
    existingLogoUrl?: string | null;
}>();
</script>

<template>
    <div class="space-y-4">
        <div>
            <InputLabel for="name_party" value="Nome do Partido" />
            <TextInput id="name_party" type="text" class="mt-1 block w-full" v-model="form.name_party"
                placeholder="Ex: Partido dos Trabalhadores" autofocus />
            <InputError class="mt-2" :message="form.errors.name_party" />
        </div>

        <div>
            <ImageUploadWithCropper v-model="form.logo" label="Logo do Partido" :error="form.errors.logo"
                :initial-image-url="existingLogoUrl" :enable-cropper="false" class="mt-1" :aspect-ratio="null" />
        </div>
    </div>
</template>