<script setup lang="ts">
import Modal from "./Modal.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { ExclamationCircleIcon } from "@heroicons/vue/24/outline";

const props = withDefaults(
    defineProps<{
        show: boolean;
        title: string;
        message: string;
        confirmButtonText?: string;
        cancelButtonText?: string;
    }>(),
    {
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
    }
);

const emit = defineEmits(["close", "confirm"]);

const confirm = () => {
    emit("confirm");
};

const close = () => {
    emit("close");
};
</script>

<template>
    <Modal :show="show" :max-width="'sm'" @close="close">
        <div class="p-6">
            <div class="flex items-start">
                <div
                    class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-800/20 dark:text-yellow-400 sm:mx-0 sm:h-10 sm:w-10"
                >
                    <ExclamationCircleIcon class="h-6 w-6" />
                </div>
                <div class="mt-0 ml-3 text-left">
                    <h3
                        class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100"
                        id="modal-title"
                    >
                        {{ title }}
                    </h3>
                    <div class="mt-2">
                        <p
                            class="text-sm text-gray-500 dark:text-gray-400"
                            v-html="message"
                        ></p>
                    </div>
                </div>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <PrimaryButton @click="confirm" class="ml-3">
                    {{ confirmButtonText }}
                </PrimaryButton>
                <button
                    @click="close"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-500 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm"
                >
                    {{ cancelButtonText }}
                </button>
            </div>
        </div>
    </Modal>
</template>
