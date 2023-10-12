<script setup>
import { Container, HH2 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

defineOptions({ layout: PublicLayout });

defineProps({
    upcomingEvents: Array,
});
</script>
<template>
    <Head title="UKCSTV: Counter-Strike Coverage for the UK & Ireland"></Head>
    <Container>
        <div class="relative mb-6 transition opacity-80 hover:opacity-100">
            <Link class="absolute inset-0" :href="route('introducing-b-site')"></Link>
            <img src="../../assets/introducing-b-site.jpg" class="object-cover object-center rounded-lg h-96 w-full shadow-lg">
        </div>
        <HH2 class="mb-6">Upcoming Events</HH2>
        <section
            class="flex flex-wrap lg:grid grid-cols-3 lg:grid-rows-2 lg:grid-cols-6 gap-3"
        >
            <div
                v-for="(event, eventIndex) in upcomingEvents"
                class="relative overflow-hidden rounded-lg h-48 sm:w-[calc(33.3%-1rem)] lg:w-auto lg:h-auto group"
                :class="{
                    'lg:row-span-2 lg:col-span-4 lg:min-h-64': eventIndex === 0,
                    'lg:aspect-square': eventIndex !== 0,
                    'hidden sm:block': eventIndex > 1,
                }"
            >
                <Link :href="route('events.show', event.id)" class="absolute inset-0 z-10"></Link>
                <div
                    class="absolute inset-0 bg-black/20 transition group-hover:bg-black/50 flex items-center justify-center"
                >
                    <img :src="event.logo" class="object-cover object-center" />
                </div>
                <div
                    class="absolute inset-x-0 bottom-0 top-2/3 bg-gradient-to-t from-black to-transparent flex text-lg items-end p-3 justify-between"
                    :class="{
                        'lg:text-sm': eventIndex > 0,
                    }"
                >
                    <div>
                        {{ event.name }}
                        <div class="text-sm">
                            <!-- {{ seriesMap.team_a_score }} - -->
                            <!-- {{ seriesMap.team_b_score }} -->
                        </div>
                    </div>
                    <div class="uppercase text-xs opacity-50">
                        <!-- {{ seriesMap.status }} -->
                    </div>
                </div>
            </div>
        </section>
    </Container>
</template>
