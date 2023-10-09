<script setup>
import BaseMatchFeedItem from "./BaseMatchFeedItem.vue";

defineProps({
    log: Object,
});
</script>
<template>
    <BaseMatchFeedItem class="border-orange-500">
        {{ log.data.userName }} planted the bomb on
        {{ log.data.bombsite }}
    </BaseMatchFeedItem>
</template>
