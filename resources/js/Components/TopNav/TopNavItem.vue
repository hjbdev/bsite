<script setup>
import { Link } from "@inertiajs/vue3";
</script>
<template>
    <Link
        class="h-8 flex items-center rounded-md justify-center px-3 dark:border-zinc-700 dark:hover:bg-zinc-900/50 transition"
    >
        <slot />
    </Link>
</template>
