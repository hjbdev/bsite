<script setup>
import {
    CardTitle,
    PrimaryButton,
    SecondaryButton,
    HH3,
    popModal,
} from "@hjbdev/ui";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    data: Object,
});

const form = useForm({
    team_id: null,
    map_id: null,
    type: null,
});

if (props.data.series.vetos.length) {
    if (props.data.series.vetos.slice(-1)[0]?.team_id === props.data.series.team_a_id) {
        console.log('a');
        form.team_id = props.data.series.team_b_id;
    } else {
        form.team_id = props.data.series.team_a_id;
    }
}

if (props.data.series.type === "bo3") {
    if (props.data.series.vetos.length < 3) {
        form.type = "ban";
    } else if (props.data.series.vetos.length < 6) {
        form.type = "pick";
    } else if (props.data.series.vetos.length === 6) {
        form.type = "left-over";
        form.team_id = null;
    }
}

if (props.data.series.type === "bo5") {
    if (props.data.series.vetos.length < 2) {
        form.type = "ban";
    } else if (props.data.series.vetos.length < 6) {
        form.type = "pick";
    } else {
        form.type = "left-over";
        form.team_id = null;
    }
}

if (props.data.series.type === 'bo1') {
    if (props.data.series.vetos.length < 6) {
        form.type = "ban";
    } else {
        form.type = "left-over";
        form.team_id = null;
    }
}



function submit() {
    form.post(route("admin.series.vetos.store", props.data.series.id), {
        onSuccess: () => {
            popModal();
        },
    });
}
</script>
<template>
    <div>
        <CardTitle class="mb-6">Veto</CardTitle>
        <div class="space-x-1 mb-12">
            <component
                :is="
                    form.team_id === data.series.team_a_id
                        ? PrimaryButton
                        : SecondaryButton
                "
                @click="form.team_id = data.series.team_a_id"
                >{{ data.series.team_a.name }}</component
            >
            <component
                :is="
                    form.team_id === data.series.team_b_id
                        ? PrimaryButton
                        : SecondaryButton
                "
                @click="form.team_id = data.series.team_b_id"
                >{{ data.series.team_b.name }}</component
            >
            <component
                :is="form.team_id === null ? PrimaryButton : SecondaryButton"
                @click="form.team_id = null"
                >Left Over</component
            >
        </div>
        <div class="space-x-1 mb-12">
            <component
                :is="form.type === 'pick' ? PrimaryButton : SecondaryButton"
                @click="form.type = 'pick'"
                >Pick</component
            >
            <component
                :is="form.type === 'ban' ? PrimaryButton : SecondaryButton"
                @click="form.type = 'ban'"
                >Ban</component
            >
            <component
                :is="
                    form.type === 'left-over' ? PrimaryButton : SecondaryButton
                "
                @click="form.type = 'left-over'"
                >Left Over</component
            >
        </div>
        <div class="space-x-1 mb-12">
            <component
                v-for="map in data.maps.sort((a, b) =>
                    a.name.localeCompare(b.name),
                )"
                :disabled="data.series.vetos.find((veto) => veto.map_id === map.id)"
                class="disabled:opacity-50 disabled:cursor-not-allowed"
                :is="form.map_id === map.id ? PrimaryButton : SecondaryButton"
                @click="form.map_id = map.id"
                >{{ map.title }}</component
            >
        </div>
        <div class="flex justify-end">
            <PrimaryButton @click="submit">Save</PrimaryButton>
        </div>
    </div>
</template>
