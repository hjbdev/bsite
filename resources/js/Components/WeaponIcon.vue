<script setup>
const props = defineProps({
    weaponName: String,
});
const svg = await import(`../../assets/weapons/${props.weaponName}.svg`);
</script>
<template>
    <img class="object-contain object-center" :src="svg.default" />
</template>
