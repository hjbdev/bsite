<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    event: Object,
});

const form = useForm({
    players: [],
    start_date: null,
    end_date: null,
    logo: null,
    prize_pool: null,
    location: null,
    delay: 0,
    ...props.event,
    name: props.event?.name ?? "",
});

function submit() {
    if (typeof form.logo === 'string') {
        form.logo = null;
    }
    if (props.event) {
        form.patch(route("admin.events.update", props.event.id));
    } else {
        form.post(route("admin.events.store"));
    }
}
</script>

<template>

    <Container class="py-6 space-y-6">
        <Head :title="event ? `Edit ${event.name}` : 'Create Event'" />

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
                name="delay"
                type="number"
                label="Delay"
                :value="form.delay"
                :error="form.errors.delay"
                @input="(v) => (form.delay = v.target.value)"
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
            <Input
                name="prize_pool"
                label="Prize Pool"
                :value="form.prize_pool"
                :error="form.errors.prize_pool"
                @input="(v) => (form.prize_pool = v.target.value)"
            />
            <Input
                name="location"
                label="Location"
                :value="form.location"
                :error="form.errors.location"
                @input="(v) => (form.location = v.target.value)"
            />
            <div class="inline-block">
                <Input
                    name="logo"
                    label="Logo"
                    type="file"
                    :error="form.errors.logo"
                    @input="(v) => (form.logo = v.target.files[0])"
                />
            </div>
            <div class="flex justify-end">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
