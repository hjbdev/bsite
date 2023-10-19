<script setup>
import { useForm } from "@inertiajs/vue3";
import { PrimaryButton, CardTitle, popModal } from "@hjbdev/ui";
import UserAutocomplete from "@/Components/Users/UserAutocomplete.vue";

const props = defineProps({
    data: Object,
});

const form = useForm({
    user: null,
    user_id: null,
});

function submit() {
    form.transform((data) => ({
        user_id: data.user.id,
    })).post(route("admin.organisers.users.store", props.data.organiser.id), {
        onSuccess: () => {
            popModal();
        }
    });
}
</script>
<template>
    <div>
        <CardTitle class="mb-6">Add User</CardTitle>
        <UserAutocomplete
            v-model="form.user"
            label="User"
            :display-value="(t) => t?.name"
            :error="form.errors.user_id ?? null"
        />
        <div class="mt-6 flex justify-end">
            <PrimaryButton @click="submit">Save</PrimaryButton>
        </div>
    </div>
</template>
