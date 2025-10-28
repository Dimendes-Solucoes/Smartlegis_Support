<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData<T = any> {
    data: T[];
    links: PaginationLink[];
    current_page?: number;
    first_page_url?: string;
    from?: number;
    last_page?: number;
    last_page_url?: string;
    next_page_url?: string | null;
    path?: string;
    per_page?: number;
    prev_page_url?: string | null;
    to?: number;
    total?: number;
}

interface Props {
    paginator: PaginatedData;
    minLinks?: number;
    showWhenEmpty?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    minLinks: 3,
    showWhenEmpty: false,
});

const shouldShow = computed(() => {
    if (props.showWhenEmpty) {
        return props.paginator.links && props.paginator.links.length > 0;
    }
    return (
        props.paginator.data &&
        props.paginator.data.length > 0 &&
        props.paginator.links &&
        props.paginator.links.length > props.minLinks
    );
});
</script>

<template>
    <div v-if="shouldShow" class="mt-6 flex justify-center">
        <Link
            v-for="(link, index) in paginator.links"
            :key="index"
            :href="link.url || ''"
            class="px-4 py-2 text-sm"
            :class="{
                'bg-indigo-500 text-white rounded-md': link.active,
                'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200': !link.active,
                'cursor-not-allowed text-gray-400 dark:text-gray-600': !link.url,
            }"
            :disabled="!link.url"
            v-html="link.label"
        />
    </div>
</template>
