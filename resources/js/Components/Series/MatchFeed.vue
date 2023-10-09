<script setup>
const matchFeedItems = import.meta.glob("./MatchFeedItems/*.vue", { eager: true });

function resolveComponent(path) {
    const item = matchFeedItems[path];
    
    if (typeof item === "undefined") {
        throw new Error(`Component not found: ${path}`);
    }

    return item.default;
}

defineProps({
    logs: Array,
});
</script>
<template>
    <div
        class="absolute inset-0 overflow-y-auto flex flex-col items-start gap-1 p-3"
    >
        <template v-for="log in logs">
            <component
                :is="resolveComponent(`./MatchFeedItems/${log.type}.vue`)"
                :log="log"
            ></component>
        </template>
    </div>
</template>
