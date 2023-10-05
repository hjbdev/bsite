<script setup>
import { Container, HH2 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";

defineOptions({ layout: PublicLayout });

defineProps({
    upcomingEvents: Array,
});
</script>
<template>
    <Container>
        <HH2 class="mb-6">Upcoming Events</HH2>
        <section
            class="flex flex-wrap lg:grid grid-cols-3 lg:grid-rows-2 lg:grid-cols-6 gap-3"
        >
            <div
                v-for="(event, eventIndex) in upcomingEvents"
                class="relative overflow-hidden rounded-lg h-48 sm:w-[calc(33.3%-1rem)] lg:w-auto lg:h-auto"
                :class="{
                    'lg:row-span-2 lg:col-span-4': eventIndex === 0,
                    'lg:aspect-square': eventIndex !== 0,
                    'hidden sm:block': eventIndex > 1,
                }"
            >
                <img
                    src="https://img-cdn.hltv.org/eventbanner/x0nhFPMFZFnn5Q_ZUEVlaa.png?ixlib=java-2.1.0&w=1276&s=d7fc2e98440b6bae9b7f89ef2b5869b8"
                    class="w-full h-full object-cover object-center"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex text-lg items-end p-3 justify-between"
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
