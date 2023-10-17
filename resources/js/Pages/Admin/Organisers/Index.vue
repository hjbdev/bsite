<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, Input } from "@hjbdev/ui";
import { nextTick, onMounted, ref, watch } from "vue";
import { useDebounceFn } from "@vueuse/core";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    organisers: Object,
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
    <Head title="Organisers" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>Organisers</HH1>
            <div>
                <PrimaryButton :as="Link" :href="route('admin.organisers.create')"
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
                v-for="organiser in organisers.data"
                :href="
                    route('admin.organisers.show', {
                        organiser: organiser.id,
                    })
                "
                class="dark:border-zinc-800 py-3 flex gap-3"
            >
                <div class="w-16">{{ organiser.id }}</div>
                <div class="w-48 truncate">{{ organiser.name }}</div>
                <div>{{ organiser.steam_id64 }}</div>
            </Link>

            <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                <Pagination :links="organisers.links" />
            </div>
        </div>
    </Container>
</template>
