<script setup>


defineProps({
    profilePic: {
        type: String,
        default: null
    },
    username: {
        type: String,
        default: null
    },
});


</script>

<template>
    <div class="flex items-center space-x-2" v-if="profilePic || username"  mb-1>
        <a  class="flex items-center space-x-2 cursor-pointer">
            <img class="w-9 h-9 rounded-full" :src="profilePic"  />
            <label class="text-gray-400">{{ username }}</label> <!-- Added light gray color -->
        </a>
    </div>
  </template>
