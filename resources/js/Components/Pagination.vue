<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>
<template>
    <Link
        v-for="l in links"
        size="small"
        v-html="l.label"
        :href="l.url ?? ''"
        class="'border border-transparent rounded-md font-semibold transition ease-in-out duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 px-2.5 py-1.5 text-sm border-zinc-300 dark:border-zinc-500 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800'"
        :class="{
            'font-bold bg-zinc-200 dark:bg-zinc-950': l.active,
            'text-zinc-500 bg-white dark:bg-zinc-800': !l.active,
        }"
    ></Link>
</template>
