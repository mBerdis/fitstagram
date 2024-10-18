<script setup>
defineProps({
    profilePic: {
        type: String,
    },
    username: {
        type: String
    },
});
</script>

<template>
    <img class="w-9 h-9 rounded-full" :src= "profilePic">
    <label>{{ username }}</label>
</template>
