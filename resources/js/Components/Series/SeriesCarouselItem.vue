<script setup>
import { Link } from "@inertiajs/vue3";
import { isToday, format, formatDistanceToNow } from "date-fns";
import FrostedGlassCard from "../FrostedGlassCard.vue";
import { QuestionMarkCircleIcon } from "@heroicons/vue/20/solid";

defineProps({
    series: Object,
});

function getDate(date) {
    const d = new Date(date);
    if (isToday(d)) {
        return format(d, "HH:mm");
    } else {
        return formatDistanceToNow(d, {
            addSuffix: true,
        });
    }
}
</script>
<template>
    <FrostedGlassCard class="relative mb-2 flex-none p-3 md:w-1/4" flush>
        <Link
            :href="
                route('matches.show.seo', {
                    match: series.id,
                    slug: series.slug,
                })
            "
            class="absolute inset-0"
        ></Link>
        <div
            class="mb-3 flex items-center gap-2 truncate text-sm text-zinc-600 dark:text-zinc-400 font-semibold"
        >
            <img
                :src="series.event?.logo"
                class="h-4 w-4 flex-none rounded object-cover font-medium"
            />
            {{ series.event?.name }}
        </div>
        <div class="mb-3 grid grid-cols-5 items-center">
            <div
                class="col-span-2 flex items-center gap-2 truncate"
            >
                <img
                    v-if="series.team_a.logo"
                    :src="series.team_a.logo"
                    class="h-4 w-4 flex-none"
                />
                <QuestionMarkCircleIcon v-else class="h-4 w-4" />
                {{ series.team_a?.name }}
            </div>
            <div
                v-if="
                    series.status === 'upcoming' ||
                    series.status === 'cancelled'
                "
                class="flex items-center justify-center opacity-50"
            >
                vs
            </div>
            <div v-else class="flex items-center justify-center">
                {{ series.team_a_score }} - {{ series.team_b_score }}
            </div>
            <div
                class="col-span-2 flex items-center justify-end gap-2 truncate"
            >
                {{ series.team_b?.name ?? series.team_b_name }}
                <img
                    v-if="series.team_b?.logo"
                    :src="series.team_b?.logo"
                    class="h-4 w-4 flex-none"
                />
                <QuestionMarkCircleIcon v-else class="h-4 w-4" />
            </div>
        </div>
        <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400">
            <span v-if="series.status === 'ongoing'" class="text-red-600"
                >&bull; LIVE</span
            >
            <span v-else-if="series.status === 'upcoming'"
                >Starts {{ getDate(series.start_date) }}</span
            >
        </div>
    </FrostedGlassCard>
</template>
