<script setup>
import { Container, HH1, HH2 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import SeriesListItem from "@/Components/Series/SeriesListItem.vue";
import { Head } from "@inertiajs/vue3";
import { format } from "date-fns";
import Pagination from "@/Components/Pagination.vue";

defineOptions({ layout: PublicLayout });

defineProps({
    event: Object,
    series: Object,
    pastSeries: Object
});
</script>
<template>
    <Head :title="event.name"></Head>
    <Container>
        <div class="flex gap-6 items-center mb-6">
            <FrostedGlassCard flush class="aspect-square w-32">
                <img :src="event.logo" class="w-full h-full object-cover" />
            </FrostedGlassCard>
            <div>
                <div class="sm:flex items-center gap-3">
                    <HH1>{{ event.name }}</HH1>
                    <div
                        v-if="event.location"
                        class="dark:text-zinc-400 text-zinc-600"
                    >
                        📍 {{ event.location }}
                    </div>
                </div>
                <div v-if="event.description" class="opacity-80">
                    {{ event.description }}
                </div>
                <div class="flex gap-x-3 flex-wrap">
                    <div v-if="event.prize_pool">
                        <strong>Prize Pool:</strong>
                        {{ event.prize_pool }}
                    </div>
                    <div v-if="event.start_date">
                        <strong>Start Date:</strong>
                        {{ format(new Date(event.start_date), "MMMM d, yyyy") }}
                    </div>
                    <div v-if="event.end_date">
                        <strong>End Date:</strong>
                        {{ format(new Date(event.end_date), "MMMM d, yyyy") }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <HH2 class="mb-3">Matches</HH2>
            <FrostedGlassCard flush class="overflow-hidden">
                <SeriesListItem v-for="game in series.data" :series="game"></SeriesListItem>
                <div v-if="!series?.data?.length" class="p-3 text-center">No Upcoming Matches</div>
            </FrostedGlassCard>
            <div class="flex justify-end mt-2 gap-1">
                <Pagination :links="series.links"></Pagination>
            </div>
        </div>
        <div>
            <HH2 class="mb-3">Results</HH2>
            <FrostedGlassCard flush class="overflow-hidden">
                <SeriesListItem v-for="game in pastSeries.data" :series="game"></SeriesListItem>
                <div v-if="!pastSeries?.data?.length" class="p-3 text-center">No Past Matches</div>
            </FrostedGlassCard>
            <div class="flex justify-end mt-2 gap-1">
                <Pagination :links="pastSeries.links"></Pagination>
            </div>
        </div>
    </Container>
</template>
