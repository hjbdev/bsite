<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, HH2 } from "@hjbdev/ui";

const props = defineProps({
    team: Object
})
</script>

<template>
    <Head :title="team.name" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1>{{ team.name }}</HH1>
                <div>
                    <PrimaryButton :as="Link" :href="route('admin.teams.edit', team.id)">Edit</PrimaryButton>
                </div>
            </div>

            <HH2>Players</HH2>
            todo
            <HH2>Matches</HH2>
            todo
        </Container>
    </AuthenticatedLayout>
</template>
