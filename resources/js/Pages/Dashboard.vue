<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Container, HH1 } from "@hjbdev/ui";
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <Container class="py-12">
            <HH1>Dashboard</HH1>
        </Container>
    </AuthenticatedLayout>
</template>
