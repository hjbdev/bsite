<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
    SelectInput,
} from "@hjbdev/ui";
import TeamAutocomplete from "@/Components/Teams/TeamAutocomplete.vue";
import EventAutocomplete from "@/Components/Events/EventAutocomplete.vue";

const props = defineProps({
    series: Object,
});

const typeOptions = [
    { name: "Best of 1", id: "bo1" },
    { name: "Best of 3", id: "bo3" },
    { name: "Best of 5", id: "bo5" },
];

const form = useForm({
    event: null,
    team_a: null,
    team_b: null,
    team_a_score: 0,
    team_b_score: 0,
    type: null,
    status: "upcoming",
    start_date: null,
    ...props.series,

    type: props.series?.type
        ? typeOptions.find((t) => t.id === props.series?.type)
        : null,
});

function submit() {
    if (props.series) {
        form.patch(route("admin.series.update", props.series.id));
    } else {
        form.post(route("admin.series.store"));
    }
}
</script>

<template>
    <Head :title="series ? `Edit Series ${series.id}` : 'Create Series'" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1 v-if="series">Edit Series {{ series?.id }}</HH1>
                <HH1 v-else>Create Series</HH1>
            </div>

            <Card class="space-y-6">
                <EventAutocomplete
                    v-model="form.event"
                    label="Event"
                    :error="form.errors['event.id'] ?? null"
                    :display-value="(t) => t?.name"
                />

                <TeamAutocomplete
                    v-model="form.team_a"
                    label="Team A"
                    :error="form.errors['team_a.id'] ?? null"
                    :display-value="(t) => t?.name"
                />
                <Input
                    label="Team A Score"
                    :value="form.team_a_score"
                    :error="form.errors['team_a_score'] ?? null"
                    type="number"
                    @input="(v) => (form.team_a_score = v.target.value)"
                />
                <TeamAutocomplete
                    v-model="form.team_b"
                    label="Team B"
                    :error="form.errors['team_b.id'] ?? null"
                    :display-value="(t) => t?.name"
                />
                <Input
                    label="Team B Score"
                    :value="form.team_b_score"
                    type="number"
                    :error="form.errors['team_b_score'] ?? null"
                    @input="(v) => (form.team_b_score = v.target.value)"
                />
                <SelectInput
                    label="Type"
                    v-model="form.type"
                    :error="form.errors['type.id'] ?? null"
                    :options="typeOptions"
                />
                <div class="flex justify-end mt-6">
                    <PrimaryButton @click="submit">Save</PrimaryButton>
                </div>
            </Card>
        </Container>
    </AuthenticatedLayout>
</template>
