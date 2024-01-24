<script setup>
defineProps({
    icon: Object,
    flush: Boolean
});
</script>
<template>
    <div
        class="font-medium flex items-center border-b-2 dark:border-zinc-900 justify-between gap-4"
        :class="{
            'p-4': !flush,
            'pb-4': flush,
        }"
    >
        <div class="flex items-center gap-2">
            <component :is="icon" class="w-4 h-4 inline-block" />
            <slot />
        </div>
        <div>
            <slot name="extra" />
        </div>
    </div>
</template>
