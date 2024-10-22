<script setup>
import { ref } from 'vue';
import UserListView from '../Generic/UserListView.vue';

defineProps({
  friends: Array,
});
</script>

<template>
 <div class="">

    <h1 class="text-gray-400">Friends</h1>
    <div v-for="friend in friends" :key="friends.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <UserListView :user="friend" />
    </div>

  </div>
</template>

<style scoped>
</style>
