<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import { Link } from "@inertiajs/vue3";
import PublicLayout from "@/Layouts/PublicLayout.vue";

defineProps({
    series: Array,
});
</script>
<template>
    <PublicLayout>
        <Container>
            <HH1 class="mb-6">Matches</HH1>
            <div class="divide-y">
                <Link
                    v-for="game in series"
                    :href="route('matches.show.seo', {
                        match: game.id,
                        slug: game.slug,
                    })"
                    class="flex gap-6 py-3 dark:border-zinc-800 items-center"
                >
                    <div class="text-xs dark:bg-zinc-700 rounded-full px-1">
                        {{ game.type }}
                    </div>
                    <div class="flex flex-col gap-2 w-48">
                        <h4 href="/" class="font-semibold">
                            {{ game.team_a.name }}
                        </h4>
                        <h4 href="/" class="font-semibold">
                            {{ game.team_b.name }}
                        </h4>
                    </div>
                    <div class="flex flex-col gap-2 w-16">
                        <div>{{ game.team_a_score }}</div>
                        <div>{{ game.team_b_score }}</div>
                    </div>
                    <div>{{ game.event.name }}</div>
                </Link>
            </div>
        </Container>
    </PublicLayout>
</template>
