<script setup>
import { AutocompleteInput } from "@hjbdev/ui";
import { ref, watch, computed } from "vue";
import { useDebounceFn } from "@vueuse/core";
import nationalities from "../../assets/nationalities.json";

const query = ref(null);
const options = ref([]);

const props = defineProps({
    modelValue: Object,
});

const nationalityItems = computed(() => {
    return nationalities.map((n) => ({
        name: n.nationality,
        id: n.alpha_2_code,
    }));
});

const search = useDebounceFn(() => {
    options.value = nationalityItems.value.filter((n) =>
        n.name.toLowerCase().includes(query.value.toLowerCase()) || n.id.toLowerCase().includes(query.value.toLowerCase()),
    ).slice(0, 10);
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
