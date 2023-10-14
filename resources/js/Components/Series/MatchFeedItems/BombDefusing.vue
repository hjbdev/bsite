<script setup>
import BaseMatchFeedItem from "./BaseMatchFeedItem.vue";

defineProps({
    log: Object,
});
</script>
<template>
    <BaseMatchFeedItem class="border-blue-500">
        {{ log.data.userName }} is defusing the bomb
    </BaseMatchFeedItem>
</template>
