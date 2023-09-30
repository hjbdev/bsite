<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";

const props = defineProps({
    teams: Array
})
</script>

<template>
    <Head title="Teams" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1>Teams</HH1>
                <div>
                    <PrimaryButton :as="Link" :href="route('admin.teams.create')">Create</PrimaryButton>
                </div>
            </div>

            <div class="divide-y">
                <Link v-for="team in teams" :href="route('admin.teams.show', team.id)" class="dark:border-zinc-800 py-3 flex gap-3">
                    <div class="w-16">{{ team.id }}</div> {{ team.name }}
                </Link>
            </div>
        </Container>
    </AuthenticatedLayout>
</template>
