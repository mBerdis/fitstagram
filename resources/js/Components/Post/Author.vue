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
    <div class="flex items-center space-x-2">
      <img class="w-9 h-9 rounded-full" :src="profilePic" />
      <label class="text-gray-400">{{ username }}</label> <!-- Added light gray color -->
    </div>
  </template>
