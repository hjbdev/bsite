<script setup>
import { computed } from "vue";
import CardSectionHeader from "./CardSectionHeader.vue";
import FrostedGlassCard from "./FrostedGlassCard.vue";
import {
    ForwardIcon,
    BackwardIcon,
    CalendarIcon,
ArrowsRightLeftIcon,
} from "@heroicons/vue/20/solid";

const props = defineProps({
    rosterMoves: Array,
});

function rosterMoveType(rosterMove) {
    if (!rosterMove?.end_date) {
        return "join";
    }

    const startDate = new Date(rosterMove.start_date);
    const endDate = new Date(rosterMove?.end_date);

    if (startDate.getTime() < endDate.getTime()) {
        return "leave";
    }

    return "join";
}

const recentRosterMovesWithTypes = computed(() => {
    return props.rosterMoves.map((rosterMove) => {
        return {
            ...rosterMove,
            type: rosterMoveType(rosterMove),
            human_date: new Date(rosterMove.most_recent_move)
                .toLocaleDateString("en-GB", {
                    // year: "numeric",
                    month: "short",
                    day: "numeric",
                })
                .replace(",", ""),
        };
    });
});
</script>
<template>
    <FrostedGlassCard flush>
        <CardSectionHeader :icon="ArrowsRightLeftIcon">Transfers</CardSectionHeader>
        <div class="pb-4">
            <div
                v-for="rosterMove in recentRosterMovesWithTypes"
                class="px-4 py-2 transition font-medium leading-loose flex gap-4 items-center"
            >
                <img
                    v-if="rosterMove.team.logo"
                    :src="rosterMove.team.logo"
                    class="h-8 w-8 rounded object-cover"
                />
                <div v-else class="h-8 w-8 rounded bg-zinc-900/30">

                </div>
                <div class="flex flex-col items-stretch">
                    <h4 class="transition">
                        {{ rosterMove.player_name }}
                    </h4>
                    <div
                        class="text-sm dark:text-zinc-400 text-zinc-500 flex gap-3"
                    >
                        <div class="flex items-center gap-1">
                            <ForwardIcon
                                v-if="rosterMove.type === 'join'"
                                class="h-4 w-4 text-green-500"
                            />
                            <BackwardIcon v-else class="h-4 w-4 text-red-500" />
                        </div>
                        <div>{{ rosterMove.team_name }}</div>
                        <div class="flex items-center gap-1">
                            <CalendarIcon class="h-4 w-4" />
                        </div>
                        <div>{{ rosterMove.human_date }}</div>
                        <div class="flex items-center gap-1">
                            <MapPinIcon class="h-4 w-4" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </FrostedGlassCard>
</template>
