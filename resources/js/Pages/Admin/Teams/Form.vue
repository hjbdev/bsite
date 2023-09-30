<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PlayerAutocomplete from "@/Components/Players/PlayerAutocomplete.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
    CardTitle,
    DangerButton,
} from "@hjbdev/ui";

const props = defineProps({
    team: Object,
});

const form = useForm({
    players: [],
    ...props.team,
    name: props.team?.name ?? "",
});

function submit() {
    if (props.team) {
        form.patch(route("admin.teams.update", props.team.id));
    } else {
        form.post(route("admin.teams.store"));
    }
}
</script>

<template>
    <Head :title="team ? `Edit ${team.name}` : 'Create Team'" />

    <AuthenticatedLayout>
        <Container class="py-6 space-y-6">
            <div class="flex items-center justify-between">
                <HH1 v-if="team">Edit {{ team?.name }}</HH1>
                <HH1 v-else>Create Team</HH1>
            </div>

            <Card>
                <Input
                    name="name"
                    label="Name"
                    :value="form.name"
                    @input="(v) => (form.name = v.target.value)"
                />
            </Card>
            <Card>
                <template #header>
                    <CardTitle>Players</CardTitle>
                </template>
                <div class="space-y-3">
                    <div v-for="(player, playerIndex) in form.players" class="flex gap-3 items-end">
                        <PlayerAutocomplete
                            label="Player"
                            v-model="form.players[playerIndex]"
                            :display-value="(t) => t.name"
                        />
                        <Input 
                            type="date"
                            label="Join Date"
                            :value="form.players[playerIndex]?.pivot?.start_date"
                            @input="(v) => (form.players[playerIndex].pivot.start_date = v.target.value)"
                        />
                        <DangerButton class="mb-0.5">Remove</DangerButton>
                    </div>
                </div>
            </Card>
            <Card
                ><div class="flex justify-end">
                    <PrimaryButton @click="submit">Save</PrimaryButton>
                </div>
            </Card>
        </Container>
    </AuthenticatedLayout>
</template>
