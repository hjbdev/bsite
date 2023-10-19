<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import OrganiserAddUserModal from "@/Components/Organisers/OrganiserAddUserModal.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { TrashIcon } from "@heroicons/vue/20/solid";
import {
    Container,
    HH1,
    PrimaryButton,
    DangerButton,
    CardTitle,
    Card,
    pushModal,
    confirmDialog,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    organiser: Object,
});

function addPlayer() {
    pushModal(OrganiserAddUserModal, {
        organiser: props.organiser,
    });
}

async function destroyPlayer(id) {
    if (await confirmDialog('Are you sure?', 'This user will no longer have access to ' + props.organiser.name)) {
        router.delete(route('admin.organisers.users.destroy', [props.organiser.id, id]));
    }
}
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

        <Card>
            <template #header>
                <CardTitle>Users</CardTitle>
            </template>
            <template #extra>
                <PrimaryButton @click="addPlayer"> Add </PrimaryButton>
            </template>

            <div
                v-for="user in organiser.users"
                class="py-3 flex justify-between items-center gap-3"
            >
                {{ user.name }}
                <DangerButton size="uniform" @click="destroyPlayer(user.id)">
                    <TrashIcon class="h-4 w-4" />
                </DangerButton>
            </div>
        </Card>
    </Container>
</template>
