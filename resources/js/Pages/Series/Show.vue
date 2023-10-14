<script setup>
import { Container, SecondaryButton, HH2 } from "@hjbdev/ui";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import useEcho from "@/Composables/useEcho";
import { Head, router } from "@inertiajs/vue3";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import MatchFeed from "@/Components/Series/MatchFeed.vue";

defineOptions({ layout: PublicLayout });

const props = defineProps({
    series: Object,
    logs: Array,
});

const echo = useEcho();
const logs = ref(props.logs ?? []);
const selectedSnapshotMap = ref();

const currentMap = ref(
    props.series?.current_series_map?.map?.title?.toLowerCase() ?? null,
);

if (!props.series?.current_series_map) {
    const ongoingMaps = props.series.series_maps?.filter(
        (m) => m.status === "ongoing",
    );
    const upcomingMaps = props.series.series_maps?.filter(
        (m) => m.status === "upcoming",
    );
    const finishedMaps = props.series.series_maps?.filter(
        (m) => m.status === "finished",
    );

    if (ongoingMaps.length) {
        currentMap.value = ongoingMaps[0].map.title.toLowerCase();
    } else if (upcomingMaps.length) {
        currentMap.value = upcomingMaps.slice(-1)[0].map.title.toLowerCase();
    } else if (finishedMaps.length) {
        currentMap.value = finishedMaps.slice(-1)[0].map.title.toLowerCase();
    }
}

onMounted(() => {
    document.getElementById(
        "bg-image",
    ).style.backgroundImage = `url(https://stratbox.app/images/maps/${currentMap.value}.jpg)`;
});

watch(currentMap, () => {
    document.getElementById(
        "bg-image",
    ).style.backgroundImage = `url(https://stratbox.app/images/maps/${currentMap.value}.jpg)`;
});

if (props.series.series_maps.length) {
    selectedSnapshotMap.value = props.series.series_maps[0].map.name;
}

onMounted(() => {
    echo.channel(`series.${props.series.id}`)
        .listen("Logs\\LogCreated", (e) => {
            logs.value.unshift(e.log);
            if (e.log?.map) {
                currentMap.value = e.log.map.replace("de_", "");
            }
            if (logs.value.length > 100) {
                logs.value.pop();
            }
        })
        .listen("Series\\SeriesUpdated", () => {
            router.reload({ only: ["series"] });
        })
        .listen("SeriesMaps\\SeriesMapUpdated", () => {
            router.reload({ only: ["series"] });
        });
});

onUnmounted(() => {
    echo.leaveChannel(`series.${props.series.id}`);
});

function padArrayEnd(arr, len, padding) {
    return arr.concat(Array(len - arr.length).fill(padding));
}

const seriesMaps = computed(() => {
    return padArrayEnd(
        props.series.series_maps,
        parseInt(props.series.type.replace("bo", "")),
        {
            map: { title: "TBD" },
        },
    );
});
</script>
<template>
    <Head
        :title="`${series.team_a.name} vs ${series.team_b.name} at ${series.event.name}`"
    />

    <Container class="space-y-6">
        <FrostedGlassCard class="flex flex-wrap sm:flex-nowrap">
            <div class="flex items-center gap-3">
                <img
                    v-if="series.team_a.logo"
                    :src="series.team_a.logo"
                    class="h-10 w-10"
                />
                <QuestionMarkCircleIcon v-else class="h-10 w-10" />
                <h4 class="text-xl sm:text-3xl font-medium tracking-tighter">
                    {{ series.team_a.name }}
                </h4>
            </div>
            <div class="ml-auto sm:mx-auto sm:text-center">
                <div span class="dark:text-zinc-500 text-zinc-200">
                    Best of {{ series.type.replace("bo", "") }}
                </div>
                <div
                    v-if="series.type === 'bo1' && seriesMaps.length"
                    class="text-2xl font-bold"
                >
                    {{ seriesMaps[0].team_a_score }}
                    <span class="dark:text-zinc-500 text-zinc-200">-</span>
                    {{ seriesMaps[0].team_b_score }}
                </div>
                <div v-else class="text-2xl font-bold">
                    {{ series.team_a_score }}
                    <span class="dark:text-zinc-500 text-zinc-200">-</span>
                    {{ series.team_b_score }}
                </div>
            </div>
            <div
                class="flex items-center gap-3 w-full justify-end sm:justify-start sm:w-auto flex-row-reverse sm:flex-row"
            >
                <h4 class="text-xl sm:text-3xl font-medium tracking-tighter">
                    {{ series.team_b.name }}
                </h4>
                <img
                    v-if="series.team_b.logo"
                    :src="series.team_b.logo"
                    class="h-10 w-10"
                />
                <QuestionMarkCircleIcon v-else class="h-10 w-10" />
            </div>
        </FrostedGlassCard>
        <HH2>Maps</HH2>
        <div
            class="grid gap-6"
            :class="{
                'lg:grid-cols-5': series.type === 'bo5',
                'lg:grid-cols-3': series.type === 'bo3',
                'lg:grid-cols-2': series.type === 'bo2',
                'lg:grid-cols-1': series.type === 'bo1',
            }"
        >
            <FrostedGlassCard
                v-for="seriesMap in seriesMaps"
                flush
                class="relative overflow-hidden h-48 group"
            >
                <img
                    v-if="seriesMap.map?.title !== 'TBD'"
                    :src="
                        series.type === 'bo1'
                            ? `https://stratbox.app/images/maps/${seriesMap.map?.title?.toLowerCase()}.jpg`
                            : `https://stratbox.app/images/maps/${seriesMap.map?.title?.toLowerCase()}_thumb.jpg`
                    "
                    class="w-full h-full object-cover transition-all group-hover:opacity-75"
                    :class="{
                        'opacity-25': seriesMap.status !== 'ongoing',
                        'opacity-75': seriesMap.status === 'ongoing',
                    }"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t flex items-end p-6 text-lg justify-between text-white"
                    :class="{
                        'from-black/20 to-transparent':
                            seriesMap.map?.title !== 'TBD' &&
                            seriesMap.status === 'ongoing',
                        'from-black/75 to-transparent':
                            seriesMap.map?.title !== 'TBD' &&
                            seriesMap.status !== 'ongoing',
                        'from-black/40 to-black/70':
                            seriesMap.map?.title === 'TBD',
                    }"
                >
                    <div>
                        {{ seriesMap.map.title }}
                        <div
                            v-if="seriesMap.map?.title !== 'TBD'"
                            class="text-sm"
                        >
                            {{ seriesMap.team_a_score }} -
                            {{ seriesMap.team_b_score }}
                        </div>
                    </div>
                    <div class="uppercase text-xs opacity-50">
                        {{ seriesMap.status }}
                    </div>
                </div>
            </FrostedGlassCard>
        </div>
        <div class="grid lg:grid-cols-2 gap-6">
            <div>
                <HH2 class="mb-6">Match Feed</HH2>
                <FrostedGlassCard class="h-64 relative overflow-hidden">
                    <!-- <img
                        :src="`https://stratbox.app/images/maps/${series?.current_series_map?.map?.title?.toLowerCase()}.jpg`"
                        alt=""
                        class="absolute inset-0 backdrop-blur-2xl"
                    /> -->
                    <MatchFeed :logs="logs" />
                </FrostedGlassCard>
            </div>
            <div>
                <HH2 class="mb-6">Streams</HH2>
                <FrostedGlassCard flush class="overflow-hidden">
                    <ul>
                        <li
                            v-for="stream in series.streams"
                            class="p-3 hover:dark:bg-black/25 hover:bg-zinc-200 transition relative"
                        >
                            <a
                                class="absolute inset-0"
                                :href="stream.url"
                                target="_blank"
                            ></a>
                            {{ stream.name }}
                        </li>
                        <li
                            v-if="!series.streams.length"
                            class="p-3 hover:dark:bg-black/25 hover:bg-zinc-200 transition relative"
                        >
                            No Streams Available
                        </li>
                    </ul>
                </FrostedGlassCard>
            </div>
        </div>

        <HH2>Scoreboard</HH2>
        <FrostedGlassCard flush class="overflow-hidden">
            <div class="p-2">
                <SecondaryButton
                    v-for="seriesMap in series.series_maps"
                    class="mr-1 last:mr-0"
                    :class="{
                        'bg-zinc-200 !dark:bg-zinc-600':
                            selectedSnapshotMap === seriesMap.map.name,
                    }"
                    @click="selectedSnapshotMap = seriesMap.map.name"
                >
                    {{ seriesMap.map.name }}
                </SecondaryButton>
            </div>
            <table
                v-if="series.series_maps.length"
                class="dark:text-white w-full"
            >
                <thead>
                    <tr>
                        <th class="p-2 text-left">Player</th>
                        <th class="p-2 text-right">Kills</th>
                        <th class="p-2 text-right">Assists</th>
                        <th class="p-2 text-right">Deaths</th>
                        <th class="p-2 text-right">ADR</th>
                    </tr>
                </thead>
                <template v-for="seriesMap in series.series_maps">
                    <tbody v-if="seriesMap.map.name === selectedSnapshotMap">
                        <tr
                            v-for="player in seriesMap.players
                                .sort((a, b) => b.pivot.damage - a.pivot.damage)
                                .sort((a, b) => b.team_id - a.team_id)"
                            :class="{
                                'bg-orange-600/20':
                                    series.team_a.players.some(
                                        (p) => p.id === player.id,
                                    ) &&
                                    series.terrorist_team_id ===
                                        series.team_a_id,
                                'bg-blue-600/20':
                                    series.team_a.players.some(
                                        (p) => p.id === player.id,
                                    ) && series.ct_team_id === series.team_a_id,
                            }"
                        >
                            <td class="p-2">{{ player.name }}</td>
                            <td class="p-2 text-right">
                                {{ player.pivot.kills }}
                            </td>
                            <td class="p-2 text-right">
                                {{ player.pivot.assists }}
                            </td>
                            <td class="p-2 text-right">
                                {{ player.pivot.deaths }}
                            </td>
                            <td class="p-2 text-right">
                                {{
                                    (
                                        player.pivot.damage /
                                        (seriesMap.rounds_played ?? 0)
                                    ).toFixed(0)
                                }}
                            </td>
                        </tr>
                    </tbody>
                </template>
            </table>
        </FrostedGlassCard>
    </Container>
</template>
