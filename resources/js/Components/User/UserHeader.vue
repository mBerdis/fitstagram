<script setup>
import { ref } from 'vue';

defineProps({
  user: Object,
});
</script>

<template>
 <div class="">

    <div class="max-w-4xl mx-auto p-4">
        <div class="flex items-center space-x-5">
        <!-- User Profile Picture -->
        <img :src="user.profile_picture" alt="Profile Picture" class="w-40 h-40 rounded-full object-cover" />

        <!-- User Info -->
        <div>
            <h1 class="text-gray-300 text-3xl font-bold">{{ user.first_name }} {{ user.last_name }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ user.username }}</p>
            <p class="text-gray-500 dark:text-gray-400">{{ user.email }}</p>
        </div>
        </div>
    </div>

  </div>
</template>

<style scoped>
</style>
