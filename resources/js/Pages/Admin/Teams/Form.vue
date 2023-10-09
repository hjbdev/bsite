<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PlayerAutocomplete from "@/Components/Players/PlayerAutocomplete.vue";
import { Head, useForm } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
    CardTitle,
    DangerButton,
} from "@hjbdev/ui";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    team: Object,
});

const form = useForm({
    players: [],
    logo: null,
    ...props.team,
    name: props.team?.name ?? "",
});

function submit() {
    if (typeof form.logo === 'string') {
        form.logo = null;
    }
    if (props.team) {
        form.patch(route("admin.teams.update", props.team.id));
    } else {
        form.post(route("admin.teams.store"));
    }
}
</script>

<template>
    <Head :title="team ? `Edit ${team.name}` : 'Create Team'" />

    <Container class="py-6 space-y-6">
        <div class="flex items-center justify-between">
            <HH1 v-if="team">Edit {{ team?.name }}</HH1>
            <HH1 v-else>Create Team</HH1>
        </div>

        <Card class="flex flex-col gap-6 items-start">
            <div class="w-full">
                <Input
                    name="name"
                    label="Name"
                    :value="form.name"
                    :error="form.errors.name"
                    accept=".png"
                    @input="(v) => (form.name = v.target.value)"
                />
            </div>
            <div>
                <Input
                    name="logo"
                    label="Logo"
                    :error="form.errors.logo"
                    type="file"
                    @input="(v) => (form.logo = v.target.files[0])"
                />
            </div>
        </Card>
        <Card>
            <template #header>
                <CardTitle>Players</CardTitle>
            </template>
            <div class="space-y-3">
                <div
                    v-for="(player, playerIndex) in form.players"
                    class="flex gap-3 items-end"
                >
                    <PlayerAutocomplete
                        label="Player"
                        v-model="form.players[playerIndex]"
                        :display-value="(t) => t.name"
                    />
                    <Input
                        type="text"
                        label="SteamID64"
                        :value="form.players[playerIndex].steam_id64"
                        @input="
                            (v) =>
                                (form.players[playerIndex].steam_id64 =
                                    v.target.value)
                        "
                    />
                    <Input
                        type="date"
                        label="Join Date"
                        :value="form.players[playerIndex]?.pivot?.start_date"
                        @input="
                            (v) =>
                                (form.players[playerIndex].pivot.start_date =
                                    v.target.value)
                        "
                    />
                    <Input
                        type="date"
                        label="Leave Date"
                        :value="form.players[playerIndex]?.pivot?.end_date"
                        @input="
                            (v) =>
                                (form.players[playerIndex].pivot.end_date =
                                    v.target.value)
                        "
                    />
                    <DangerButton
                        class="mb-0.5"
                        @click="form.players.splice(playerIndex, 1)"
                        >Remove</DangerButton
                    >
                </div>
                <div class="flex justify-end">
                    <PrimaryButton
                        @click="
                            form.players.push({
                                id: null,
                                name: null,
                                steam_id64: null,
                                pivot: {
                                    start_date: null,
                                },
                            })
                        "
                        >Add</PrimaryButton
                    >
                </div>
            </div>
        </Card>
        <Card
            ><div class="flex justify-end">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
