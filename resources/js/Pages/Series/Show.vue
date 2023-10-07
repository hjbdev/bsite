<script setup>
import { Container, HH1, SecondaryButton, HH2, Card } from "@hjbdev/ui";
import { QuestionMarkCircleIcon } from "@heroicons/vue/24/solid";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { computed, onMounted, onUnmounted, ref, watch } from "vue";
import useEcho from "@/Composables/useEcho";
import WeaponIcon from "@/Components/WeaponIcon.vue";
import { Head } from "@inertiajs/vue3";
import FrostedGlassCard from "@/Components/FrostedGlassCard.vue";

defineOptions({ layout: PublicLayout });

const props = defineProps({
    series: Object,
    snapshot: Object,
    logs: Array,
});

const echo = useEcho();

const logs = ref(props.logs ?? []);

const snapshot = ref(props.snapshot);
const selectedSnapshotMap = ref();

const currentMap = ref(
    props.series?.current_series_map?.map?.title?.toLowerCase(),
);

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

if (Object.keys(props.snapshot?.maps ?? {})[0] ?? null) {
    selectedSnapshotMap.value = Object.keys(props.snapshot?.maps ?? {})[0];
}

onMounted(() => {
    echo.channel(`series.${props.series.id}`)
        .listen("Logs\\LogCreated", (e) => {
            logs.value.unshift(e.log);
            if (e.log?.map) {
                currentMap.value = e.log.map.replace("de_", "");
            }
        })
        .listen("Series\\SeriesSnapshot", (e) => {
            snapshot.value = e.snapshot;
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
        <FrostedGlassCard class="flex">
            <div class="flex items-center gap-3">
                <img
                    v-if="series.team_a.logo"
                    :src="series.team_a.logo"
                    class="h-10 w-10"
                />
                <QuestionMarkCircleIcon v-else class="h-10 w-10" />
                <h4 class="text-3xl font-medium tracking-tighter">
                    {{ series.team_a.name }}
                </h4>
            </div>
            <div class="mx-auto text-center">
                <div span class="dark:text-zinc-500 text-zinc-200">
                    Best of {{ series.type.replace("bo", "") }}
                </div>
                <div v-if="series.type === 'bo1' && seriesMaps.length" class="text-2xl font-bold">
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
            <div class="flex items-center gap-3">
                <h4 class="text-3xl font-medium tracking-tighter">
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
                    class="w-full h-full object-cover opacity-25 transition-all group-hover:opacity-75"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-6 text-lg justify-between"
                    :class="{
                        'from-black to-transparent':
                            seriesMap.map?.title !== 'TBD',
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
                <FrostedGlassCard class="h-64 relative">
                    <!-- <img
                        :src="`https://stratbox.app/images/maps/${series?.current_series_map?.map?.title?.toLowerCase()}.jpg`"
                        alt=""
                        class="absolute inset-0 backdrop-blur-2xl"
                    /> -->
                    <div
                        class="absolute inset-0 overflow-y-auto flex flex-col items-start gap-1 p-3"
                    >
                        <template v-for="log in logs">
                            <div
                                v-if="log.type === 'Blinded'"
                                class="flex gap-1 px-1 py-0.5 border-blue-800 border-2 rounded"
                            >
                                <span
                                    :class="{
                                        'text-orange-400':
                                            log.data.throwerTeam ===
                                            'TERRORIST',
                                        'text-blue-400':
                                            log.data.throwerTeam === 'CT',
                                    }"
                                >
                                    {{ log.data.throwerName }}
                                </span>
                                blinded
                                <Suspense>
                                    <WeaponIcon
                                        weapon-name="flashbang"
                                        class="h-6"
                                    />
                                </Suspense>
                                <span
                                    :class="{
                                        'text-orange-400':
                                            log.data.victimTeam === 'TERRORIST',
                                        'text-blue-400':
                                            log.data.victimTeam === 'CT',
                                    }"
                                    >{{ log.data.victimName }}</span
                                >
                                for {{ log.data.time }}s
                            </div>
                            <div
                                v-if="log.type === 'Kill'"
                                :key="`log${log.id}`"
                                class="flex gap-1 px-1 py-0.5 border-red-800 border-2 rounded"
                            >
                                <span
                                    :class="{
                                        'text-orange-400':
                                            log.data.killerTeam === 'TERRORIST',
                                        'text-blue-400':
                                            log.data.killerTeam === 'CT',
                                    }"
                                >
                                    {{ log.data.killerName }}
                                </span>
                                <Suspense>
                                    <WeaponIcon
                                        :weapon-name="log.data.weapon"
                                        class="h-6"
                                    />
                                </Suspense>
                                <img
                                    v-if="log.data.headshot"
                                    class="invert h-5"
                                    src="../../../assets/headshot_icon.webp"
                                />
                                <span
                                    :class="{
                                        'text-orange-400':
                                            log.data.killedTeam === 'TERRORIST',
                                        'text-blue-400':
                                            log.data.killedTeam === 'CT',
                                    }"
                                >
                                    {{ log.data.killedName }}
                                </span>
                            </div>
                            <div
                                v-if="log.type === 'RoundEnd'"
                                :key="`log${log.id}`"
                                class="px-1 py-0.5 border border-zinc-600 rounded"
                            >
                                Round Ended
                            </div>
                            <div
                                v-if="log.type === 'MatchStatus'"
                                :key="`log${log.id}`"
                                class="px-1 py-0.5 border border-zinc-600 rounded"
                            >
                                Score is {{ log.data.scoreA }}:{{
                                    log.data.scoreB
                                }}
                            </div>
                            <div
                                v-if="log.type === 'BombPlanting'"
                                :key="`log${log.id}`"
                                class="px-1 py-0.5 border border-orange-500 rounded"
                            >
                                {{ log.data.userName }} planted the bomb on
                                {{ log.data.bombsite }}
                            </div>
                        </template>
                    </div>
                </FrostedGlassCard>
            </div>
            <div>
                <HH2 class="mb-6">Streams</HH2>
                <FrostedGlassCard flush class="overflow-hidden">
                    <ul>
                        <li
                            class="p-3 hover:dark:bg-black/25 hover:bg-zinc-200 transition"
                        >
                            index
                        </li>
                        <li
                            class="p-3 hover:dark:bg-black/25 hover:bg-zinc-200 transition"
                        >
                            EPICLAN1
                        </li>
                    </ul>
                </FrostedGlassCard>
            </div>
        </div>

        <HH2>Scoreboard</HH2>
        <FrostedGlassCard flush>
            <div class="p-2">
                <SecondaryButton
                    v-for="mapName in Object.keys(snapshot?.maps ?? {})"
                    class="mr-1 last:mr-0"
                    :class="{
                        '!bg-zinc-600': selectedSnapshotMap === mapName,
                    }"
                    @click="selectedSnapshotMap = mapName"
                >
                    {{ mapName }}
                </SecondaryButton>
            </div>
            <table
                v-if="snapshot.maps[selectedSnapshotMap]?.players"
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
                <tbody>
                    <tr
                        v-for="player in snapshot.maps[
                            selectedSnapshotMap
                        ].players.filter((p) => p.team === 'TERRORIST')"
                        class="bg-red-600/20"
                    >
                        <td class="p-2">{{ player.name }}</td>
                        <td class="p-2 text-right">{{ player.kills }}</td>
                        <td class="p-2 text-right">{{ player.assists }}</td>
                        <td class="p-2 text-right">{{ player.deaths }}</td>
                        <td class="p-2 text-right">
                            {{
                                (
                                    player.damage /
                                    snapshot.maps[selectedSnapshotMap]
                                        .roundsPlayed
                                ).toFixed(0)
                            }}
                        </td>
                    </tr>
                </tbody>
                <tbody class="bg-blue-600/20">
                    <tr
                        v-for="player in snapshot.maps[
                            selectedSnapshotMap
                        ].players.filter((p) => p.team === 'CT')"
                    >
                        <td class="p-2">{{ player.name }}</td>
                        <td class="p-2 text-right">{{ player.kills }}</td>
                        <td class="p-2 text-right">{{ player.assists }}</td>
                        <td class="p-2 text-right">{{ player.deaths }}</td>
                        <td class="p-2 text-right">
                            {{
                                (
                                    player.damage /
                                    snapshot.maps[selectedSnapshotMap]
                                        .roundsPlayed
                                ).toFixed(0)
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </FrostedGlassCard>
    </Container>
</template>
