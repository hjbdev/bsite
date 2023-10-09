<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import { Head, Link } from "@inertiajs/vue3";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import { isToday, format, formatDistanceToNow } from "date-fns";
import Pagination from "@/Components/Pagination.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import SeriesListItem from "@/Components/Series/SeriesListItem.vue";

defineOptions({ layout: PublicLayout });

defineProps({
    series: Object,
    title: String
});

</script>
<template>
    <Container>
        <Head :title="title"></Head>
        <HH1 class="mb-6">{{ title }}</HH1>
        <div class="divide-y">
            <SeriesListItem
                v-for="game in series.data"
                :series="game"
            />
            <div v-if="!series?.data?.length" class="text-center opacity-50 mb-6">
                No Matches Found
            </div>
            <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                <Pagination :links="series.links" />
            </div>
        </div>
    </Container>
</template>
