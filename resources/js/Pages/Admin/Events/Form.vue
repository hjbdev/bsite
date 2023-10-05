<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PlayerAutocomplete from "@/Components/Players/PlayerAutocomplete.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
    CardTitle,
    DangerButton,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    event: Object,
});

const form = useForm({
    players: [],
    start_date: null,
    end_date: null,
    ...props.event,
    name: props.event?.name ?? "",
});

function submit() {
    if (props.event) {
        form.patch(route("admin.events.update", props.event.id));
    } else {
        form.post(route("admin.events.store"));
    }
}
</script>

<template>
    <Head :title="event ? `Edit ${event.name}` : 'Create Event'" />

    <Container class="py-6 space-y-6">
        <div class="flex items-center justify-between">
            <HH1 v-if="event">Edit {{ event?.name }}</HH1>
            <HH1 v-else>Create Event</HH1>
        </div>

        <Card class="space-y-6">
            <Input
                name="name"
                label="Name"
                :value="form.name"
                :error="form.errors.name"
                @input="(v) => (form.name = v.target.value)"
            />
            <Input
                name="description"
                label="Description"
                :value="form.description"
                :error="form.errors.description"
                @input="(v) => (form.description = v.target.value)"
            />
            <Input
                type="date"
                label="Start Date"
                :value="form.start_date"
                :error="form.errors.start_date"
                @input="(v) => (form.start_date = v.target.value)"
            />
            <Input
                type="date"
                label="End Date"
                :value="form.end_date"
                :error="form.errors.end_date"
                @input="(v) => (form.end_date = v.target.value)"
            />
            <div class="flex justify-end">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
