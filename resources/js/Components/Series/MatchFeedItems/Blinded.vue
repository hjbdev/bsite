<script setup>
import BaseMatchFeedItem from "./BaseMatchFeedItem.vue";
import WeaponIcon from "@/Components/WeaponIcon.vue";

defineProps({
    log: Object,
});
</script>
<template>
    <BaseMatchFeedItem class="border-blue-500 dark:border-blue-800">
        <span
            :class="{
                'text-orange-400': log.data.throwerTeam === 'TERRORIST',
                'text-blue-400': log.data.throwerTeam === 'CT',
            }"
        >
            {{ log.data.throwerName }}
        </span>
        blinded
        <Suspense>
            <WeaponIcon weapon-name="flashbang" class="h-6" />
        </Suspense>
        <span
            :class="{
                'text-orange-400': log.data.victimTeam === 'TERRORIST',
                'text-blue-400': log.data.victimTeam === 'CT',
            }"
            >{{ log.data.victimName }}</span
        >
        for {{ log.data.time }}s</BaseMatchFeedItem
    >
</template>
