<script setup>
import { SidebarLayout, Container, HH2, FadeTransition } from "@hjbdev/ui";
import TopNavItem from "@/Components/TopNav/TopNavItem.vue";
import { Link } from "@inertiajs/vue3";

const appName = import.meta.env.VITE_APP_NAME || "Colosseum";
</script>
<template>
    <SidebarLayout class="dark:text-white h-screen">
        <div
            class="absolute inset-0 bg-cover z-0 blur-md [mask-image:linear-gradient(to_bottom_left,transparent_25%,rgba(0,0,0,0.25)_75%)]"
            style="
                background-image: url(&quot;https://stratbox.app/images/maps/ancient.jpg&quot;);
            "
        ></div>

        <div
            class="dark:bg-zinc-800/50 bg-zinc-100/50 backdrop-blur-md h-16 shadow-md sticky top-0 mb-6"
        >
            <Container class="flex justify-between gap-6 items-center h-full">
                <div>
                    <Link href="/">
                        <HH2>{{ appName }}</HH2>
                    </Link>
                </div>
                <div class="flex dark:border-zinc-900">
                    <TopNavItem href="/matches">Matches</TopNavItem>
                    <TopNavItem href="/teams">Teams</TopNavItem>
                    <TopNavItem href="/players">Players</TopNavItem>
                </div>
            </Container>
        </div>

        <FadeTransition mode="out-in">
            <div class="relative z-[1]" :key="$page.url">
                <slot />
            </div>
        </FadeTransition>
    </SidebarLayout>
</template>
