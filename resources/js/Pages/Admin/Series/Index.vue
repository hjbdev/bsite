<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, Input, SecondaryButton } from "@hjbdev/ui";
import Pagination from "@/Components/Pagination.vue";
import { ref } from "vue";
import { onMounted } from "vue";
import { nextTick } from "vue";
import { watch } from "vue";
import { useDebounceFn } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    series: Object,
    event: Object
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

if (url.searchParams.has("filter[search]")) {
    searchQuery.value = url.searchParams.get("filter[search]");
}

watch(
    searchQuery,
    useDebounceFn((q, oldQ) => {
        if (!hasMounted.value) return;
        const url = new URL(window.location.href);
        url.searchParams.set("filter[search]", q);
        router.visit(url);
    }, 500),
);

function clearFilter(key) {
    const url = new URL(window.location.href);
    url.searchParams.delete(`filter[${key}]`);
    router.visit(url);
}
</script>

<template>
    <Head title="Matches" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>Matches</HH1>
            <div class="space-x-1">
                <SecondaryButton v-if="event" :as="Link" :href="route('admin.events.show', event.id)">Back to {{ event.name }}</SecondaryButton>
                <PrimaryButton :as="Link" :href="route('admin.series.create')"
                    >Create</PrimaryButton
                >
            </div>
        </div>

        <div class="flex justify-between items-center">
            <div>
                <div v-if="event" class="border dark:border-zinc-600 px-2 py-0.5 rounded" @click="clearFilter('event_id')">
                    <strong>Event:</strong> {{ event.name }}
                    <button class="py-0.25 px-1">&times;</button>
                </div>
            </div>
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
