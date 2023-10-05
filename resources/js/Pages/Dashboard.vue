<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Container, HH1 } from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });
</script>

<template>
    <Head title="Dashboard" />

    <Container class="py-6">
        <HH1>Dashboard</HH1>
    </Container>
</template>
