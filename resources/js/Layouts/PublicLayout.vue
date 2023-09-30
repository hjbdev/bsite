<script setup>
import { SidebarLayout, Container, HH2 } from "@hjbdev/ui";
import TopNavItem from "@/Components/TopNav/TopNavItem.vue";
</script>
<template>
    <SidebarLayout class="dark:text-white">
        <div class="dark:bg-zinc-800 bg-zinc-100 h-16 shadow-md sticky top-0 mb-6">
            <Container class="flex justify-between gap-6 items-center h-full">
                <div>
                    <HH2>Colosseum</HH2>
                </div>
                <div class="flex divide-x dark:border-zinc-900">
                    <TopNavItem href="/matches">Matches</TopNavItem>
                    <TopNavItem href="/teams">Teams</TopNavItem>
                    <TopNavItem href="/players">Players</TopNavItem>
                </div>
            </Container>
        </div>

        <slot />
    </SidebarLayout>
</template>
