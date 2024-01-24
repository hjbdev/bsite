<script setup>
import { HH1 } from "@hjbdev/ui";
import Container from "@/Components/Container.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import SeriesListItem from "@/Components/Series/SeriesListItem.vue";
import { Head } from "@inertiajs/vue3";
import { format } from "date-fns";
import Pagination from "@/Components/Pagination.vue";
import CardSectionHeader from "@/Components/CardSectionHeader.vue";
import { ServerStackIcon } from "@heroicons/vue/20/solid";

defineOptions({ layout: PublicLayout });

defineProps({
    event: Object,
    series: Object,
    pastSeries: Object,
});
</script>
<template>
    <Head :title="event.name"></Head>
    <Container class="space-y-6">
        <div class="flex gap-6 items-center">
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
            <FrostedGlassCard flush class="overflow-hidden">
                <CardSectionHeader :icon="ServerStackIcon">
                    Upcoming
                    <template #extra>
                        <div
                            v-if="series?.links"
                            class="flex justify-end gap-1"
                        >
                            <Pagination :links="series.links" v-if="pastSeries.links?.length > 3"></Pagination>
                        </div>
                    </template>
                </CardSectionHeader>
                <SeriesListItem
                    v-for="game in series.data"
                    :series="game"
                ></SeriesListItem>
                <div v-if="!series?.data?.length" class="p-3 text-center dark:text-zinc-500 text-zinc-600 text-sm">
                    No Upcoming Matches
                </div>
            </FrostedGlassCard>
        </div>
        <div>
            <FrostedGlassCard flush class="overflow-hidden">
                <CardSectionHeader :icon="ServerStackIcon"
                    >Results
                    <template #extra>
                        <div
                            v-if="series?.links"
                            class="flex justify-end gap-1"
                        >
                            <Pagination v-if="pastSeries.links?.length > 3" :links="pastSeries.links"></Pagination>
                        </div>
                    </template>
                </CardSectionHeader>
                <SeriesListItem
                    v-for="game in pastSeries.data"
                    :series="game"
                ></SeriesListItem>
                <div v-if="!pastSeries?.data?.length" class="p-3 text-center dark:text-zinc-500 text-zinc-600 text-sm">
                    No Past Matches
                </div>
            </FrostedGlassCard>
        </div>
    </Container>
</template>
