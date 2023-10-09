<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import NationalityAutocomplete from "@/Components/NationalityAutocomplete.vue";
import {
    Container,
    HH1,
    PrimaryButton,
    Card,
    Input,
} from "@hjbdev/ui";
import nationalities from "../../../../assets/nationalities.json";
import { computed } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps({
    player: Object,
});

const nationalityItems = computed(() => {
    return nationalities.map((n) => ({
        name: n.nationality,
        id: n.alpha_2_code,
    }));
});

const form = useForm({
    name: null,
    full_name: null,
    nationality: props.player ? nationalityItems.value.find(n => n.id === props.player.nationality) : null,
    steam_id3: null,
    steam_id64: null,
    picture: null,
    birthday: null,
    ...props.player,
});

function submit() {
    if (typeof form.picture === 'string') {
        form.picture = null;
    }
    if (props.player) {
        form.patch(route("admin.players.update", props.player.id));
    } else {
        form.post(route("admin.players.store"));
    }
}
</script>

<template>
    <Head :title="player ? `Edit Player ${player.id}` : 'Create Player'" />

    <Container class="py-6">
        <div class="flex items-center justify-between mb-6">
            <HH1 v-if="player">Edit Player {{ player?.id }}</HH1>
            <HH1 v-else>Create Player</HH1>
        </div>

        <Card class="space-y-6">
            <Input
                label="Name"
                :value="form.name"
                :error="form.errors['name'] ?? null"
                @input="(v) => (form.name = v.target.value)"
            />

            <Input
                label="Full Name"
                :value="form.full_name"
                :error="form.errors['full_name'] ?? null"
                @input="(v) => (form.full_name = v.target.value)"
            />

            <div>
                <Input
                    name="picture"
                    label="Picture"
                    :error="form.errors.picture"
                    type="file"
                    @input="(v) => (form.picture = v.target.files[0])"
                />
            </div>

            <NationalityAutocomplete
                v-model="form.nationality"
                label="Nationality"
                :error="form.errors['nationality'] ?? null"
                :display-value="(t) => t?.name || t"
            />

            <Input
                label="Steam ID 64"
                :value="form.steam_id64"
                :error="form.errors['steam_id64'] ?? null"
                @input="(v) => (form.steam_id64 = v.target.value)"
            />

            <Input
                label="Steam ID 3"
                help-text="Please INCLUDE the square brackets [U:0:1234123]"
                :value="form.steam_id3"
                :error="form.errors['steam_id3'] ?? null"
                @input="(v) => (form.steam_id3 = v.target.value)"
            />

            <Input
                label="Birthday"
                type="date"
                :value="form.birthday"
                :error="form.errors['birthday'] ?? null"
                @input="(v) => (form.birthday = v.target.value)"
            />

            <div class="flex justify-end mt-6">
                <PrimaryButton @click="submit">Save</PrimaryButton>
            </div>
        </Card>
    </Container>
</template>
