<script setup>
import Container from "@/Components/Container.vue";
import TopNavItem from "@/Components/TopNav/TopNavItem.vue";
import { Link } from "@inertiajs/vue3";
import { useScroll } from "@vueuse/core";
import { ref } from "vue";

const container = ref(null);
const { y } = useScroll(container);
</script>
<template>
    <div
        class="min-h-screen bg-zinc-100 dark:bg-zinc-900 relative dark:text-white h-screen"
    >
        <div class="flex w-full h-full max-h-screen z-10 relative">
            <section ref="container" class="flex-grow overflow-y-auto pb-12 z-[11]">
                <div
                    id="bg-image"
                    class="absolute inset-0 bg-cover z-0 blur-sm [mask-image:linear-gradient(to_bottom_left,rgba(0,0,0,0.0)_50%,rgba(0,0,0,0.15)_100%)]"
                    style="
                        background-image: url(&quot;https://stratbox.app/images/maps/ancient.jpg&quot;);
                    "
                ></div>

                <div
                    class="h-16 sticky top-0 mb-6 z-30 transition duration-500"
                    :class="{
                        'dark:bg-zinc-800/50 bg-zinc-100/50 backdrop-blur-md':
                            y > 20,
                    }"
                >
                    <Container
                        class="flex justify-between gap-6 items-center h-full"
                    >
                        <div>
                            <Link href="/">
                                <img
                                    src="../../assets/bsite.svg"
                                    class="h-12"
                                />
                                <!-- <HH2>{{ appName }}</HH2> -->
                            </Link>
                        </div>
                        <div class="flex dark:border-zinc-900">
                            <TopNavItem href="/matches">Matches</TopNavItem>
                            <TopNavItem href="/results">Results</TopNavItem>
                            <!-- <TopNavItem href="/teams">Teams</TopNavItem> -->
                            <!-- <TopNavItem href="/players">Players</TopNavItem> -->
                        </div>
                    </Container>
                </div>

                <div class="relative z-[5]">
                    <slot />
                </div>
            </section>
        </div>
    </div>
</template>
