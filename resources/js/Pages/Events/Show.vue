<script setup>
import { Container, HH1 } from "@hjbdev/ui";
import PublicLayout from "@/Layouts/PublicLayout.vue";
import { Head } from "@inertiajs/vue3";

defineOptions({ layout: PublicLayout });

defineProps({
    event: Object,
});
</script>
<template>
    <Head :title="event.name"></Head>
    <Container>
        <div class="flex gap-6 items-center">
            <img
                :src="event.logo"
                class="aspect-square w-32 border-2 rounded-md"
            />
            <div>
                <HH1>{{ event.name }}</HH1>
                <div v-if="event.description" class="opacity-80">
                    {{ event.description }}
                </div>
            </div>
        </div>
    </Container>
</template>
