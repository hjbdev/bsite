<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    teams: Object,
});
</script>

<template>
    <Head title="Teams" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1>Teams</HH1>
                <div>
                    <PrimaryButton
                        :as="Link"
                        :href="route('admin.teams.create')"
                        >Create</PrimaryButton
                    >
                </div>
            </div>

            <div class="divide-y">
                <Link
                    v-for="team in teams.data"
                    :href="route('admin.teams.show', {
                        team: team.id
                    })"
                    class="dark:border-zinc-800 py-3 flex gap-3"
                >
                    <div class="w-16">{{ team.id }}</div>
                    {{ team.name }}
                </Link>

                <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                    <Pagination :links="teams.links" />
                </div>
            </div>
        </Container>
    </AuthenticatedLayout>
</template>
