<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    HH3,
    PrimaryButton,
    SecondaryButton,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    event: Object,
});
</script>

<template>
    <Head :title="event.name" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <HH1>{{ event.name }}</HH1>
                <HH3 v-if="event.organiser">Organised by <Link :href="route('admin.organisers.show', event.organiser?.id)" class="font-semibold">{{ event.organiser?.name }}</Link></HH3>
            </div>
            <div class="space-x-1">
                <SecondaryButton
                    :as="Link"
                    :href="
                        route('admin.series.index') +
                        `?filter[event_id]=${event.id}`
                    "
                    >Matches</SecondaryButton
                >
                <PrimaryButton
                    :as="Link"
                    :href="route('admin.events.edit', event.id)"
                    >Edit</PrimaryButton
                >
            </div>
        </div>

        <img :src="event.logo" />
    </Container>
</template>
