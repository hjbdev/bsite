<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { onMounted, ref } from "vue";
import useEcho from "@/Composables/useEcho";

const props = defineProps({
    series: Object,
});

const echo = useEcho();

const logs = ref([]);

onMounted(() => {
    echo.channel(`series.${props.series.id}.logs`)
        .listen("Logs\\LogCreated", (e) => {
            logs.value.push(e.log);
        });
});
</script>
<template>
    <PublicLayout>
        <Container>
            <HH1 class="mb-6">{{ series.team_a.name }} v {{ series.team_b.name }}</HH1>
            <div class="h-64 bg-black text-white w-full">
                <template v-for="log in logs">
                    <div v-if="log.type === 'Kill'">{{ log.data.userName }} killed {{ log.data.killedUserName }} with {{ log.data.weapon }} {{ log.data.headshot }}</div>
                    <div v-if="log.type === 'RoundEnd'">Round Ended</div>
                    <div v-if="log.type === 'MatchStatus'">Score is {{ log.data.scoreA }}:{{ log.data.scoreB }}</div>
                </template>
            </div>                
        </Container>
    </PublicLayout>
</template>
