<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, Card, CardTitle } from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    series: Object,
});

const baseUrl = window.location.origin;
</script>

<template>
    <Head title="series" />

    <Container class="py-6 space-y-6">
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

        <Card>
            <template #header>
                <CardTitle>Maps</CardTitle>
            </template>
            <template #extra>
                <PrimaryButton>Veto</PrimaryButton>
            </template>
            <div class="flex gap-6 flex-wrap">
                <div
                    v-for="seriesMap in series.series_maps"
                    class="relative overflow-hidden rounded-lg"
                >
                    <img
                        :src="`https://stratbox.app/images/maps/${seriesMap.map?.title?.toLowerCase()}_thumb.jpg`"
                        class="w-80"
                    />
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-6 text-lg justify-between"
                    >
                        <div>
                            {{ seriesMap.map.title }}:
                            {{ seriesMap.team_a_score }} -
                            {{ seriesMap.team_b_score }}
                        </div>
                        <div>
                            {{ seriesMap.status }}
                        </div>
                    </div>
                </div>
                <div v-if="!series.series_maps.length">No maps yet</div>
            </div>
        </Card>

        <Card>
            <template #header>
                <CardTitle>Command</CardTitle>
            </template>
            <code class="block"
                >logaddress_add_http "{{
                    baseUrl === "http://localhost"
                        ? "http://192.168.5.137"
                        : baseUrl
                }}/api/log-handler</code
            >
        </Card>
    </Container>
</template>
