<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import NationalityAutocomplete from "@/Components/NationalityAutocomplete.vue";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    organiser: Object,
});

const form = useForm({
    name: null,
    logo: null,
    ...props.organiser,
});

function submit() {
    if (typeof form.logo === 'string') {
        form.logo = null;
    }
    if (props.organiser) {
        form.patch(route("admin.organisers.update", props.organiser.id));
    } else {
        form.post(route("admin.organisers.store"));
    }
}
</script>

<template>
    <Head :title="organiser ? `Edit Organiser ${organiser.id}` : 'Create Organiser'" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1 v-if="organiser">Edit Organiser {{ organiser?.id }}</HH1>
            <HH1 v-else>Create Organiser</HH1>
        </div>

        <Card class="space-y-6">
            <Input
                label="Name"
                :value="form.name"
                :error="form.errors['name'] ?? null"
                @input="(v) => (form.name = v.target.value)"
            />

            <div>
                <Input
                    name="logo"
                    label="Logo"
                    :error="form.errors.logo"
                    type="file"
                    @input="(v) => (form.logo = v.target.files[0])"
                />
            </div>

            <div class="flex justify-end mt-6">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
