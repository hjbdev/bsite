<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    organiser: Object,
});
</script>

<template>
    <Head :title="organiser.name" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>{{ organiser.name }}</HH1>
            <div class="space-x-1">
                <PrimaryButton
                    :as="Link"
                    :href="route('admin.organisers.edit', organiser.id)"
                    >Edit</PrimaryButton
                >
            </div>
        </div>

        <img :src="organiser.logo" />

        Coming soon.
    </Container>
</template>
