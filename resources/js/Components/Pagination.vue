<script setup>
import { Link } from "@inertiajs/vue3";
import { PrimaryButton, SecondaryButton } from "@hjbdev/ui";

defineProps({
    links: Array,
});
</script>
<template>
    <component
        v-for="l in links"
        :is="l.active ? PrimaryButton : SecondaryButton"
        :as="Link"
        size="small"
        v-html="l.label"
        :href="l.url ?? ''"
        :class="{
            'font-bold': l.active,
            'text-zinc-500': !l.active,
        }"
    ></component>
</template>
