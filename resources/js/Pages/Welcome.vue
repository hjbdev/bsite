<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import HH2 from "@/Components/HH2.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import Container from "@/Components/Container.vue";
import EventList from "@/Components/Events/EventList.vue";
import RosterMovesList from "@/Components/RosterMovesList.vue";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import SeriesCarouselItem from "@/Components/Series/SeriesCarouselItem.vue";

defineOptions({ layout: PublicLayout });

const props = defineProps({
    recentRosterMoves: Array,
    upcomingEvents: Array,
    upcomingSeries: Array,
    pastEvents: Array,
    news: Array,
});

router.reload({
    only: ["news"],
});
</script>
<template>
    <Container>
        <Head
            title="B-Site: Counter-Strike Coverage for the UK & Ireland"
        ></Head>

        <div v-if="upcomingSeries?.length">
            <div class="mb-4 flex w-full gap-3 overflow-x-scroll">
                <SeriesCarouselItem v-for="series in upcomingSeries" :series="series" />
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <section class="md:col-span-2">
                <div v-if="news">
                    <div class="mb-6 flex items-center gap-3">
                        <HH2>News</HH2>
                        via
                        <a href="https://ukcsgo.com" target="_blank"
                            ><img
                                class="h-8 rounded bg-white"
                                src="../../assets/ukcsgo-logo.png"
                                alt="UKCSGO Logo"
                        /></a>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        <div
                            v-for="post in news"
                            class="group relative aspect-video overflow-hidden rounded-xl"
                        >
                            <a
                                class="absolute inset-0 z-10"
                                target="_blank"
                                :href="post.url + '?utm_source=bsite'"
                            ></a>
                            <div
                                class="ukcsgo-news absolute inset-0"
                                v-html="post.image"
                            ></div>
                            <div
                                class="absolute inset-0 flex items-end bg-gradient-to-t from-black/75 to-transparent p-6"
                            >
                                <div
                                    class="text-white transition group-hover:text-orange-500"
                                >
                                    {{ post.title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="flex flex-col gap-6">
                <EventList
                    v-if="upcomingEvents?.length || pastEvents?.length"
                    :events="[...upcomingEvents, ...pastEvents]"
                ></EventList>
                <RosterMovesList :roster-moves="recentRosterMoves" />
            </section>
        </div>
    </Container>
</template>
<style lang="postcss">
.ukcsgo-news img {
    @apply h-full w-full object-cover;
}
</style>
