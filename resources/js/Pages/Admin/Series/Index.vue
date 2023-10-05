<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, Input } from "@hjbdev/ui";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import { nextTick } from "vue";
import { watch } from "vue";
import { useDebounceFn } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    series: Object,
});

const hasMounted = ref(false);
const searchQuery = ref("");
const searchInput = ref(null);

onMounted(() => {
    nextTick(() => {
        hasMounted.value = true;
        if (searchQuery.value) {
            searchInput.value?.$el.querySelector("input")?.focus();
        }
    });
});

const url = new URL(window.location.href);

if (url.searchParams.has("search")) {
    searchQuery.value = url.searchParams.get("search");
}

watch(
    searchQuery,
    useDebounceFn((q, oldQ) => {
        if (!hasMounted.value) return;
        const url = new URL(window.location.href);
        url.searchParams.set("search", q);
        router.visit(url);
    }, 500),
);
</script>

<template>
    <Head title="series" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>Series</HH1>
            <div>
                <PrimaryButton :as="Link" :href="route('admin.series.create')"
                    >Create</PrimaryButton
                >
            </div>
        </div>

        <div class="flex justify-end">
            <Input
                ref="searchInput"
                placeholder="Search"
                :value="searchQuery"
                @input="(v) => (searchQuery = v.target.value)"
            />
        </div>

        <div class="divide-y">
            <Link
                v-for="game in series.data"
                :href="route('admin.series.show', game.id)"
                class="dark:border-zinc-800 py-3 flex gap-3"
            >
                <div class="w-16">{{ game.id }}</div>
                <div>
                    <h4>{{ game.team_a.name }}</h4>
                    <h4>{{ game.team_b.name }}</h4>
                </div>
            </Link>
            <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                <Pagination :links="series.links" />
            </div>
        </div>
    </Container>
</template>
