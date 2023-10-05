<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, HH2 } from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    event: Object,
});
</script>

<template>
    <Head :title="event.name" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>{{ event.name }}</HH1>
            <div>
                <PrimaryButton
                    :as="Link"
                    :href="route('admin.events.edit', event.id)"
                    >Edit</PrimaryButton
                >
            </div>
        </div>

        <img :src="event.logo">

        <HH2>Players</HH2>
        todo
        <HH2>Matches</HH2>
        todo
    </Container>
</template>
