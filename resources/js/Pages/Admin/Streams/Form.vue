<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton, Card, Input } from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    seriesId: Number,
});

const form = useForm({
    name: "",
    platform: "",
    url: "",
});

function submit() {
    form.post(route("admin.series.streams.store", { match: props.seriesId }));
}
</script>

<template>
    <Head title="Create Stream" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1>Create Stream</HH1>
        </div>

        <Card class="space-y-6">
            <Input
                label="Name"
                :value="form.name"
                :error="form.errors['name'] ?? null"
                @input="(v) => (form.name = v.target.value)"
            />
            <Input
                label="Platform"
                :value="form.platform"
                :error="form.errors['platform'] ?? null"
                @input="(v) => (form.platform = v.target.value)"
            />
            <Input
                label="Url"
                :value="form.url"
                :error="form.errors['url'] ?? null"
                @input="(v) => (form.url = v.target.value)"
            />
            <div class="flex justify-end mt-6">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
