<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";

const props = defineProps({
    series: Object,
});

const baseUrl = window.location.origin;
</script>

<template>
    <Head title="series" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1
                    >Series {{ series.id }} - {{ series.team_a.name }} vs
                    {{ series.team_b.name }}</HH1
                >
                <div>
                    <PrimaryButton
                        :as="Link"
                        :href="route('admin.series.edit', series.id)"
                        >Edit</PrimaryButton
                    >
                </div>
            </div>

            <div>
                Command:
                <code class="block"
                    >logaddress_add_http "{{ baseUrl }}/api/log-handler?secret={{
                        series.secret
                    }}"</code
                >
            </div>
        </Container>
    </AuthenticatedLayout>
</template>
