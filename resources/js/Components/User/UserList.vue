<script setup>
import { ref } from 'vue';
import UserListView from '../Generic/UserListView.vue';

defineProps({
  users: Array,
});
</script>

<template>
 <div class="">

    <div v-for="user in users" :key="users.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <UserListView :user="user" />
    </div>

  </div>
</template>

<style scoped>
</style>
