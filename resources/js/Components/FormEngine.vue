<script setup>
import { Input } from "@hjbdev/ui";

const props = defineProps({
    form: Object,
    fields: Array,
});

const emit = defineEmits(['update:form']);

function updateField(field, value) {
    // emit('update:form', {...props.form, [field]: value});
    // direct mutation of a prop is bad
    // but the "proper" way above doesn't work
    props.form[field] = value;
}
</script>
<template>
    <div class="space-y-6">
        <template v-for="field in fields">
            <template v-if="field.type === 'text'">
                <Input
                    :name="field.name"
                    :label="field.label"
                    :value="form[field.name]"
                    :error="form.errors[field.name] ?? null"
                    @input="(v) => updateField(field.name, v.target.value)"
                />
            </template>
            <template v-else-if="field.type === 'file'">
                <Input
                    :name="field.name"
                    :label="field.label"
                    :error="form.errors[field.name] ?? null"
                    type="file"
                    @input="(v) => updateField(field.name, v.target.files[0])"
                />
            </template>
        </template>
    </div>
</template>
