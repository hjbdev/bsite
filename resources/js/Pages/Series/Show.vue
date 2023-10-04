<script setup>
import { Container, HH1, SecondaryButton } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { computed, onMounted, ref } from "vue";
import useEcho from "@/Composables/useEcho";

const props = defineProps({
    series: Object,
    snapshot: Object,
    logs: Array,
});

const echo = useEcho();

const logs = ref(props.logs ?? []);
const reversedLogs = computed(() => {
    return logs.value.slice().reverse();
});

const snapshot = ref(props.snapshot);
const selectedSnapshotMap = ref();

if (Object.keys(props.snapshot?.maps ?? {})[0] ?? null) {
    selectedSnapshotMap.value = Object.keys(props.snapshot?.maps ?? {})[0];
}

onMounted(() => {
    echo.channel(`series.${props.series.id}`)
        .listen("Logs\\LogCreated", (e) => {
            logs.value.push(e.log);
        })
        .listen("Series\\SeriesSnapshot", (e) => {
            snapshot.value = e.snapshot;
        });
});
</script>
<template>
    <PublicLayout>
        <Container class="space-y-6">
            <HH1 class="mb-6"
                >{{ series.team_a.name }} v {{ series.team_b.name }}</HH1
            >
            <div class="h-64 bg-black text-white w-full overflow-y-auto">
                <template v-for="log in reversedLogs">
                    <div v-if="log.type === 'Kill'">
                        {{ log.data.killerName }} killed
                        {{ log.data.killedName }} with
                        {{ log.data.weapon }} {{ log.data.headshot }}
                    </div>
                    <div v-if="log.type === 'RoundEnd'">Round Ended</div>
                    <div v-if="log.type === 'MatchStatus'">
                        Score is {{ log.data.scoreA }}:{{ log.data.scoreB }}
                    </div>
                    <div v-if="log.type === 'BombPlanting'">
                        {{ log.data.userName }} planted the bomb on {{ log.data.bombsite }}
                    </div>
                </template>
            </div>
            <div>
                <SecondaryButton v-for="mapName in Object.keys(snapshot?.maps ?? {})" @click="selectedSnapshotMap = mapName">
                    {{ mapName }}
                </SecondaryButton>
                <table v-if="snapshot.maps[selectedSnapshotMap]?.players" class="dark:text-white w-full">
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
                            v-for="player in snapshot.maps[selectedSnapshotMap]
                                .players.filter(p => p.side === 'TERRORIST')"
                             class="bg-gradient-to-tr dark:from-orange-900 dark:to-orange-800"
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
                    <tbody class="bg-gradient-to-tr dark:from-blue-900 dark:to-blue-800">
                        <tr
                            v-for="player in snapshot.maps[selectedSnapshotMap]
                                .players.filter(p => p.side === 'CT')"
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
            </div>
        </Container>
    </PublicLayout>
</template>
