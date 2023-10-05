<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import { Link } from "@inertiajs/vue3";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import { isToday, format, formatDistanceToNow } from "date-fns";
import Pagination from "@/Components/Pagination.vue";
import PublicLayout from "@/Layouts/PublicLayout.vue";

defineOptions({ layout: PublicLayout });

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
    // return
}
</script>
<template>
    <Container>
        <HH1 class="mb-6">Matches</HH1>
        <div class="divide-y">
            <Link
                v-for="game in series.data"
                :href="
                    route('matches.show.seo', {
                        match: game.id,
                        slug: game.slug,
                    })
                "
                class="flex gap-6 py-3 dark:border-zinc-800 items-center"
            >
                <div
                    class="flex flex-col justify-center items-center text-xs w-20 text-center"
                >
                    {{ getDate(game.start_date) }}
                    <div class="text-xs dark:bg-zinc-700 rounded-full px-1 mt-1">
                        {{ game.type }}
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-48">
                    <h4 href="/" class="font-semibold flex gap-2 items-center">
                        <img v-if="game.team_a.logo" :src="game.team_a.logo" class="w-6 h-6" />
                        <QuestionMarkCircleIcon v-else class="w-6 h-6" />
                        {{ game.team_a.name }}
                    </h4>
                    <h4 href="/" class="font-semibold flex gap-2 items-center">
                        <img v-if="game.team_b.logo" :src="game.team_b.logo" class="w-6 h-6"/>
                        <QuestionMarkCircleIcon v-else class="w-6 h-6" />
                        {{ game.team_b.name }}
                    </h4>
                </div>
                <div class="flex flex-col gap-2 w-16">
                    <div>{{ game.team_a_score }}</div>
                    <div>{{ game.team_b_score }}</div>
                </div>
                <div>{{ game.event.name }}</div>
            </Link>
            <div class="flex justify-end dark:border-zinc-800 gap-1 pt-6">
                <Pagination :links="series.links" />
            </div>
        </div>
    </Container>
</template>
