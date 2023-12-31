<script setup>
import {
    SidebarLayout,
    Sidebar,
    SidebarItem,
    SidebarItemGroup,
    Modals,
    Dialogs,
} from "@hjbdev/ui";
import { Link, router } from "@inertiajs/vue3";
import {
    HomeIcon,
    CalendarDaysIcon,
    PuzzlePieceIcon,
    UserGroupIcon,
    UserIcon,
    BuildingOffice2Icon,
} from "@heroicons/vue/20/solid";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

function impersonate() {
    const id = prompt("Enter the ID of the user you would like to impersonate:");

    if (id) {
        router.visit(route('impersonate', id));
    } else {
        router.visit(route('impersonate.leave'));
    }
}
</script>

<template>
    <SidebarLayout class="dark:text-white">
        <Dialogs />
        <Modals />
        <template #sidebar>
            <Sidebar>
                <SidebarItemGroup>
                    <SidebarItem
                        :as="Link"
                        :href="route('dashboard')"
                        :icon="HomeIcon"
                        :active="route().current('dashboard')"
                        >Dashboard</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.events.index')"
                        :icon="CalendarDaysIcon"
                        :active="route().current('admin.events.index')"
                        >Events</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.series.index')"
                        :icon="PuzzlePieceIcon"
                        :active="route().current('admin.series.index')"
                        >Matches</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.organisers.index')"
                        :icon="BuildingOffice2Icon"
                        :active="route().current('admin.organisers.index')"
                        >Organisers</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.players.index')"
                        :icon="UserIcon"
                        :active="route().current('admin.players.index')"
                        >Players</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.teams.index')"
                        :icon="UserGroupIcon"
                        :active="route().current('admin.teams.index')"
                        >Teams</SidebarItem
                    >
                    <SidebarItem
                        :as="Link"
                        :href="route('admin.users.index')"
                        :icon="UserIcon"
                        :active="route().current('admin.users.index')"
                        >Users</SidebarItem
                    >
                </SidebarItemGroup>
                <SidebarItemGroup>
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button
                                    type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-zinc-500 dark:text-zinc-400 bg-white dark:bg-zinc-800 hover:text-zinc-700 dark:hover:text-zinc-300 focus:outline-none transition ease-in-out duration-150"
                                >
                                    {{ $page.props.auth.user.name }}

                                    <svg
                                        class="ml-2 -mr-0.5 h-4 w-4"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink v-if="$page.props.auth['user.roles'].includes('super-admin')" :href="''" as="button" @click="impersonate">
                                Impersonate
                            </DropdownLink>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </SidebarItemGroup>
            </Sidebar>
        </template>
        <slot />
    </SidebarLayout>
</template>
