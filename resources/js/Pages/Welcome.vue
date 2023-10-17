<script setup>
import { Container, HH2 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import { Head, Link } from "@inertiajs/vue3";

defineOptions({ layout: PublicLayout });

defineProps({
    upcomingEvents: Array,
    pastEvents: Array,
    news: Array,
});
</script>
<template>
    <Head title="B-Site: Counter-Strike Coverage for the UK & Ireland"></Head>
    <Container>
        <div class="relative mb-6 transition gap-6 group">
            <div
                class="absolute inset-0 flex items-center p-6 text-3xl font-bold gap-6 z-10"
            >
                <img src="../../assets/bsite.svg" class="h-16" />
                <span class="[text-shadow:_0px_2px_4px_rgba(0,_0,_0,_0.75)]">
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
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <HH2 class="mb-6">Events</HH2>
                <FrostedGlassCard flush>
                    <Link
                        v-for="(event, eventIndex) in upcomingEvents"
                        :href="route('events.show', event.id)"
                        class="p-4 flex gap-3 items-center hover:bg-black/25 transition rounded-xl"
                    >
                        <img
                            :src="event.logo"
                            class="h-8 w-8 rounded-lg object-cover"
                        />
                        <div>
                            <h4 class="text-lg">{{ event.name }}</h4>
                            <div>
                                📍
                                <span class="opacity-50">{{
                                    event.location
                                }}</span>
                            </div>
                        </div>
                        <div class="ml-auto">
                            {{
                                event.start_date.split("-").reverse().join("-")
                            }}
                        </div>
                    </Link>
                </FrostedGlassCard>
            </div>
            <div v-if="pastEvents.length">
                <HH2 class="mb-6">Past Events</HH2>
                <FrostedGlassCard flush>
                    <Link
                        v-for="(event, eventIndex) in pastEvents"
                        :href="route('events.show', event.id)"
                        class="p-4 flex gap-3 items-center hover:bg-black/25 transition rounded-xl"
                    >
                        <img
                            :src="event.logo"
                            class="h-8 w-8 rounded-lg object-cover"
                        />
                        <div>
                            <h4 class="text-lg">{{ event.name }}</h4>
                            <div>
                                📍
                                <span class="opacity-50">{{
                                    event.location
                                }}</span>
                            </div>
                        </div>
                        <div class="ml-auto">
                            {{
                                event.start_date.split("-").reverse().join("-")
                            }}
                        </div>
                    </Link>
                </FrostedGlassCard>
            </div>
        </div>
        <div>
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
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div
                    v-for="post in news"
                    class="relative aspect-video rounded-xl overflow-hidden"
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
                        class="absolute inset-0 flex items-end p-6 bg-gradient-to-t from-black to-transparent"
                    >
                        {{ post.title }}
                    </div>
                </div>
            </div>
        </div>
    </Container>
</template>
<style lang="postcss">
.ukcsgo-news img {
    @apply object-cover w-full h-full;
}
</style>
