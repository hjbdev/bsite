<script setup>
import { Link } from "@inertiajs/vue3";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import { isToday, format, formatDistanceToNow } from "date-fns";

defineProps({
    series: Object,
});

function getDate(date) {
    const d = new Date(date);
    if (isToday(d)) {
        return format(d, "HH:mm");
    } else {
        return formatDistanceToNow(d, new Date());
    }
}
</script>
<template>
    <Link
        :href="
            route('matches.show.seo', {
                match: series.id,
                slug: series.slug,
            })
        "
        class="flex gap-6 py-3 dark:border-zinc-800 items-center transition dark:hover:bg-zinc-900/50"
    >
        <div
            class="flex flex-col justify-center items-center text-xs w-20 text-center"
        >
            {{ getDate(series.start_date) }}
            <div class="text-xs dark:bg-zinc-700 rounded-full px-1 mt-1">
                {{ series.type }}
            </div>
            <div class="text-xs rounded-full px-1 mt-1 opacity-50">
                {{ series.status }}
            </div>
        </div>
        <div class="flex flex-col gap-2 w-48">
            <h4 href="/" class="font-semibold flex gap-2 items-center">
                <img
                    v-if="series.team_a.logo"
                    :src="series.team_a.logo"
                    class="w-6 h-6"
                />
                <QuestionMarkCircleIcon v-else class="w-6 h-6" />
                {{ series.team_a.name }}
            </h4>
            <h4 href="/" class="font-semibold flex gap-2 items-center">
                <img
                    v-if="series.team_b?.logo"
                    :src="series.team_b.logo"
                    class="w-6 h-6"
                />
                <QuestionMarkCircleIcon v-else class="w-6 h-6" />
                {{ series.team_b?.name ?? series.team_b_name }}
            </h4>
        </div>
        <div class="flex flex-col gap-2 w-16">
            <template
                v-if="series.type === 'bo1' && series.series_maps?.length"
            >
                <div
                    :class="{
                        'text-green-500':
                            series.series_maps[0].team_a_score >
                            series.series_maps[0].team_b_score,
                        'text-red-500':
                            series.series_maps[0].team_a_score <
                            series.series_maps[0].team_b_score,
                    }"
                >
                    {{ series.series_maps[0].team_a_score }}
                </div>
                <div
                    :class="{
                        'text-green-500':
                            series.series_maps[0].team_a_score <
                            series.series_maps[0].team_b_score,
                        'text-red-500':
                            series.series_maps[0].team_a_score >
                            series.series_maps[0].team_b_score,
                    }"
                >
                    {{ series.series_maps[0].team_b_score }}
                </div>
            </template>
            <template v-else>
                <div
                    :class="{
                        'text-green-500':
                            series.team_a_score > series.team_b_score,
                        'text-red-500':
                            series.team_a_score < series.team_b_score,
                    }"
                >
                    {{ series.team_a_score }}
                </div>
                <div
                    :class="{
                        'text-green-500':
                            series.team_a_score < series.team_b_score,
                        'text-red-500':
                            series.team_a_score > series.team_b_score,
                    }"
                >
                    {{ series.team_b_score }}
                </div>
            </template>
        </div>
        <div v-if="series.event">{{ series.event.name }}</div>
    </Link>
</template>
