<script setup>
import { Link } from "@inertiajs/vue3";
import { MapPinIcon, CalendarIcon, ClockIcon } from "@heroicons/vue/20/solid";
import FrostedGlassCard from "../FrostedGlassCard.vue";
import CardSectionHeader from "../CardSectionHeader.vue";

defineProps({
    events: Array,
});
</script>
<template>
    <FrostedGlassCard flush>
        <CardSectionHeader :icon="CalendarIcon">Events</CardSectionHeader>
        <template v-for="event in events">
            <Link
                :href="
                    route('events.show.seo', {
                        match: event.id,
                        slug: event.slug,
                    })
                "
                class="p-4 flex gap-3 items-center transition group"
            >
                <img
                    :src="event.logo"
                    class="h-8 w-8 rounded object-cover"
                />
                <div class="font-medium leading-loose">
                    <h4 class="group-hover:text-orange-500 transition">
                        {{ event.name }}
                    </h4>
                    <div
                        class="text-sm dark:text-zinc-400 text-zinc-500 flex gap-3"
                    >
                        <div class="flex items-center gap-1">
                            <ClockIcon class="h-4 w-4" />
                            <div
                                v-if="event.is_ongoing"
                                class="text-green-500 font-bold"
                            >
                                Ongoing
                            </div>
                            <div v-else>Upcoming</div>
                        </div>
                        <div class="flex items-center gap-1">
                            <CalendarIcon class="h-4 w-4" />
                            <div v-if="event.is_ongoing">
                                Ends {{ event.end_date_short_friendly }}
                            </div>
                            <div v-else>
                                {{ event.start_date_short_friendly }}
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <MapPinIcon class="h-4 w-4" />
                            <div class="truncate">
                                <span>{{ event.location }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </Link>

            <div class="mx-16 h-[2px] dark:bg-zinc-900/50"></div>
        </template>
        <div class="p-4">
            <Link
                :href="route('events.index')"
                class="text-center py-2 flex items-center justify-center gap-1.5 rounded-lg dark:bg-zinc-700/50 dark:hover:bg-zinc-700/80 transition text-sm font-medium w-full"
            >
                <CalendarIcon class="h-4 w-4 inline-block" />
                View All Events
            </Link>
        </div>
    </FrostedGlassCard>
</template>
