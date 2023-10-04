<script setup>
import { AutocompleteInput } from "@hjbdev/ui";
import { ref, watch } from "vue";
import { useDebounceFn } from "@vueuse/core";

const query = ref(null);
const options = ref([]);

const props = defineProps({
    modelValue: Object,
});

const search = useDebounceFn(() => {
    if (query.value.length < 2) {
        return;
    }

    axios
        .get(route("admin.events.search", { search: query.value }))
        .then((response) => {
            options.value = response.data;
        });
}, 500);

watch(query, search);
</script>
<template>
    <AutocompleteInput
        :model-value="modelValue"
        v-model:query="query"
        :options="options"
        @update:modelValue="$emit('update:modelValue', $event)"
    />
</template>
