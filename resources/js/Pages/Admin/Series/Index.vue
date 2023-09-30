<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";

const props = defineProps({
    series: Array,
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
                    v-for="game in series"
                    :href="route('admin.series.show', game.id)"
                    class="dark:border-zinc-800 py-3 flex gap-3"
                >
                    <div class="w-16">{{ game.id }}</div>
                    <div>
                        <h4>{{ game.team_a.name }}</h4>
                        <h4>{{ game.team_b.name }}</h4>
                    </div>
                </Link>
            </div>
        </Container>
    </AuthenticatedLayout>
</template>
