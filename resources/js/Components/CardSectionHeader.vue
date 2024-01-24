<script setup>
defineProps({
    icon: Object,
});
</script>
<template>
    <div
        class="p-4 font-medium flex items-center gap-2 border-b-2 dark:border-zinc-900"
    >
        <component :is="icon" class="w-4 h-4 inline-block" />
        <slot />
    </div>
</template>
