<script setup>
import HH2 from "@/Components/HH2.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import Container from "@/Components/Container.vue";
import EventList from "@/Components/Events/EventList.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import RosterMovesList from "@/Components/RosterMovesList.vue";
import { nextTick } from "vue";

defineOptions({ layout: PublicLayout });

const props = defineProps({
    recentRosterMoves: Array,
    upcomingEvents: Array,
    pastEvents: Array,
    news: Array,
});

router.reload({
    only: ['news'],
})
</script>
<template>
    <Head title="B-Site: Counter-Strike Coverage for the UK & Ireland"></Head>
    <Container>
        <div class="grid md:grid-cols-3 gap-6">
            <section class="md:col-span-2">
                <div class="relative mb-6 transition gap-6 group">
                    <div
                        class="absolute inset-0 flex items-center p-6 text-3xl font-bold gap-6 z-10"
                    >
                        <img src="../../assets/bsite.svg" class="h-16" />
                        <span
                            class="[text-shadow:_0px_2px_4px_rgba(0,_0,_0,_0.75)]"
                        >
                            Introducing B-Site for the UK & Ireland
                        </span>
                    </div>
                    <Link
                        class="absolute inset-0 z-20"
                        :href="route('introducing-b-site')"
                    ></Link>
                    <img
                        src="../../assets/bsite-banner.jpg"
                        class="object-cover object-center rounded-lg h-32 w-full shadow-lg opacity-80 transition group-hover:opacity-100"
                    />
                </div>
                <div v-if="news">
                    <div class="flex gap-3 mb-6 items-center">
                        <HH2>News</HH2>
                        via
                        <a href="https://ukcsgo.com" target="_blank"
                            ><img
                                class="h-8 bg-white rounded"
                                src="../../assets/ukcsgo-logo.png"
                                alt="UKCSGO Logo"
                        /></a>
                    </div>
                    <div class="grid lg:grid-cols-2 gap-3">
                        <div
                            v-for="post in news"
                            class="relative aspect-video rounded-xl overflow-hidden group"
                        >
                            <a
                                class="inset-0 absolute z-10"
                                target="_blank"
                                :href="post.url + '?utm_source=bsite'"
                            ></a>
                            <div
                                class="absolute inset-0 ukcsgo-news"
                                v-html="post.image"
                            ></div>
                            <div
                                class="absolute inset-0 flex items-end p-6 bg-gradient-to-t from-black/75 to-transparent"
                            >
                                <div class="group-hover:text-orange-500 transition">
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
    @apply object-cover w-full h-full;
}
</style>
