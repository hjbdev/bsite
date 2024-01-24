<script setup>
import HH2 from "@/Components/HH2.vue";
import Container from "@/Components/Container.vue";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import useEcho from "@/Composables/useEcho";
import { Head, router } from "@inertiajs/vue3";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";
import MatchFeed from "@/Components/Series/MatchFeed.vue";
import CardSectionHeader from "@/Components/CardSectionHeader.vue";
import { QueueListIcon, TableCellsIcon, TvIcon } from "@heroicons/vue/20/solid";

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
        <FrostedGlassCard class="grid grid-cols-2 gap-4 md:grid-cols-5">
            <div
                class="order-1 flex items-center gap-3 md:order-1 md:col-span-2"
            >
                <img
                    v-if="series.team_a.logo"
                    :src="series.team_a.logo"
                    class="h-10 w-10"
                />
                <QuestionMarkCircleIcon v-else class="h-10 w-10" />
                <h4 class="text-xl font-medium tracking-tighter sm:text-3xl">
                    {{ series.team_a.name }}
                </h4>
            </div>
            <div
                class="order-3 col-span-2 text-center md:order-2 md:col-span-1"
            >
                <div span class="text-zinc-200 dark:text-zinc-500">
                    Best of {{ series.type.replace("bo", "") }}
                </div>
                <div
                    v-if="series.type === 'bo1' && seriesMaps.length"
                    class="text-2xl font-bold"
                >
                    {{ seriesMaps[0].team_a_score }}
                    <span class="text-zinc-200 dark:text-zinc-500">-</span>
                    {{ seriesMaps[0].team_b_score }}
                </div>
                <div v-else class="text-2xl font-bold">
                    {{ series.team_a_score }}
                    <span class="text-zinc-200 dark:text-zinc-500">-</span>
                    {{ series.team_b_score }}
                </div>
            </div>
            <div
                class="order-2 flex items-center justify-end gap-3 md:col-span-2"
            >
                <h4 class="text-xl font-medium tracking-tighter sm:text-3xl">
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
                class="group relative h-48 overflow-hidden"
            >
                <img
                    v-if="seriesMap.map?.title !== 'TBD'"
                    :src="
                        series.type === 'bo1'
                            ? `https://stratbox.app/images/maps/${seriesMap.map?.title?.toLowerCase()}.jpg`
                            : `https://stratbox.app/images/maps/${seriesMap.map?.title?.toLowerCase()}_thumb.jpg`
                    "
                    class="h-full w-full object-cover transition-all group-hover:opacity-75"
                    :class="{
                        'opacity-25': seriesMap.status !== 'ongoing',
                        'opacity-75': seriesMap.status === 'ongoing',
                    }"
                />
                <div
                    class="absolute inset-0 flex items-end justify-between bg-gradient-to-t p-6 text-lg text-white"
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
                    <div class="text-xs uppercase opacity-50">
                        {{ seriesMap.status }}
                    </div>
                </div>
            </FrostedGlassCard>
        </div>
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <FrostedGlassCard flush>
                    <CardSectionHeader :icon="QueueListIcon"
                        >Match Feed</CardSectionHeader
                    >
                    <div class="relative h-64 overflow-hidden">
                        <MatchFeed :logs="logs" />
                    </div>
                </FrostedGlassCard>
            </div>
            <div>
                <FrostedGlassCard flush class="overflow-hidden">
                    <CardSectionHeader :icon="TvIcon"
                        >Streams</CardSectionHeader
                    >
                    <ul>
                        <li
                            v-for="stream in series.streams"
                            class="relative p-3 transition hover:bg-zinc-200 hover:dark:bg-black/25"
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
                            class="relative p-3 transition hover:bg-zinc-200 hover:dark:bg-black/25"
                        >
                            No Streams Available
                        </li>
                    </ul>
                </FrostedGlassCard>
            </div>
        </div>

        <FrostedGlassCard flush class="overflow-hidden">
            <CardSectionHeader :icon="TableCellsIcon"
                >Scoreboard

                <template #extra>
                    <button
                        v-for="seriesMap in series.series_maps"
                        class="mr-1 inline-flex items-center rounded-md border border-transparent border-zinc-300 bg-white px-2.5 py-1.5 text-sm font-semibold text-zinc-700 transition duration-150 ease-in-out last:mr-0 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-zinc-500 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:focus:ring-offset-zinc-800"
                        :class="{
                            '!dark:bg-zinc-600 bg-zinc-200':
                                selectedSnapshotMap === seriesMap.map.name,
                        }"
                        @click="selectedSnapshotMap = seriesMap.map.name"
                    >
                        {{ seriesMap.map.name }}
                    </button>
                </template></CardSectionHeader
            >
            <table
                v-if="series.series_maps.length"
                class="w-full dark:text-white"
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
                                    (series.team_a.players.some(
                                        (p) => p.id === player.id,
                                    ) &&
                                        series.terrorist_team_id ===
                                            series.team_a_id) ||
                                    (series.team_b.players.some(
                                        (p) => p.id === player.id,
                                    ) &&
                                        series.terrorist_team_id ===
                                            series.team_b_id),
                                'bg-blue-600/20':
                                    (series.team_a.players.some(
                                        (p) => p.id === player.id,
                                    ) &&
                                        series.ct_team_id ===
                                            series.team_a_id) ||
                                    (series.team_b.players.some(
                                        (p) => p.id === player.id,
                                    ) &&
                                        series.ct_team_id === series.team_b_id),
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
