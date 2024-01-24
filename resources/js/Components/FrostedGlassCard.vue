<script setup>
defineProps({
    flush: {
        type: Boolean,
        default: false
    }
});
</script>
<template>
    <div
        class="z-10 dark:bg-zinc-800/80 relative backdrop-blur-2xl rounded-2xl overflow-hidden"
        :class="{
            'p-6': !flush
        }"
    >
        <slot />
    </div>
</template>
