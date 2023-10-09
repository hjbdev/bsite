<script setup>
import BaseMatchFeedItem from "./BaseMatchFeedItem.vue";
import WeaponIcon from "@/Components/WeaponIcon.vue";

defineProps({
    log: Object,
});
</script>
<template>
    <BaseMatchFeedItem class="border-red-800">
        <span
            :class="{
                'text-orange-400': log.data.killerTeam === 'TERRORIST',
                'text-blue-400': log.data.killerTeam === 'CT',
            }"
        >
            {{ log.data.killerName }}
        </span>
        <Suspense>
            <WeaponIcon :weapon-name="log.data.weapon" class="h-6" />
        </Suspense>
        <img
            v-if="log.data.headshot"
            class="invert h-5"
            src="../../../../assets/headshot_icon.webp"
        />
        <span
            :class="{
                'text-orange-400': log.data.killedTeam === 'TERRORIST',
                'text-blue-400': log.data.killedTeam === 'CT',
            }"
        >
            {{ log.data.killedName }}
        </span>
    </BaseMatchFeedItem>
</template>
