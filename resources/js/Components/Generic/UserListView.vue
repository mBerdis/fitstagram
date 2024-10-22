<script setup>

defineProps({
    user: {
        type: Object
    }
});


</script>

<template>
    <div class="flex items-center space-x-2" v-if="user.profilePic || user.username"  mb-1>
        <a  class="flex items-center space-x-2 cursor-pointer">
            <img class="w-9 h-9 rounded-full" :src="user.profile_picture"  />
            <label class="text-gray-400">{{ user.username }}</label>
        </a>
    </div>
  </template>
