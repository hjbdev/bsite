<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, SecondaryButton } from "@hjbdev/ui";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    series: Object,
});
</script>

<template>
    <Head title="series" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1>Series</HH1>
                <div>
                    <PrimaryButton
                        :as="Link"
                        :href="route('admin.series.create')"
                        >Create</PrimaryButton
                    >
                </div>
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
    </AuthenticatedLayout>
</template>
