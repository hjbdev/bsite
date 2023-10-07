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
        class="mask z-10 dark:bg-zinc-800/20 relative backdrop-blur-2xl rounded-2xl after:absolute after:inset-0 after:rounded-2xl after:bg-gradient-to-br after:from-white/50 after:via-white/10 after:to-white/50 after:p-px"
        :class="{
            'p-6': !flush
        }"
    >
        <slot />
    </div>
</template>
