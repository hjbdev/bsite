<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Container, HH1, PrimaryButton } from "@hjbdev/ui";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    events: Object,
});
</script>

<template>
    <Head title="Events" />

    <AuthenticatedLayout>
        <Container class="py-6">
            <div class="flex items-center justify-between mb-6">
                <HH1>Events</HH1>
                <div>
                    <PrimaryButton
                        :as="Link"
                        :href="route('admin.events.create')"
                        >Create</PrimaryButton
                    >
                </div>
            </div>

            <div class="divide-y">
                <Link
                    v-for="event in events.data"
                    :href="route('admin.events.show', {
                        event: event.id
                    })"
                    class="dark:border-zinc-800 py-3 flex gap-3"
                >
                    <div class="w-16">{{ event.id }}</div>
                    {{ event.name }}
                </Link>

                <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                    <Pagination :links="events.links" />
                </div>
            </div>
        </Container>
    </AuthenticatedLayout>
</template>
