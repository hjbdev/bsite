<script setup>
import BaseMatchFeedItem from "./BaseMatchFeedItem.vue";

defineProps({
    log: Object,
});
</script>
<template>
    <BaseMatchFeedItem class="border-zinc-200 dark:border-zinc-600">
        Round Ended
    </BaseMatchFeedItem>
</template>
