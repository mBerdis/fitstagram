<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  friendRequests: Array,
});

const handleAccept = (requestId) => {
  router.post(route('friendRequest.accept', requestId), {}, {
    onSuccess: () => {
      console.log('Friend request accepted');
    },
  });
};

const handleDecline = (requestId) => {
  router.post(route('friendRequest.decline', requestId), {}, {
    onSuccess: () => {
      console.log('Friend request declined');
    },
  });
};
</script>

<template>
  <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-lg">
    <h2 class="text-xl font-semibold mb-4">Friend Requests</h2>
    <div v-if="friendRequests.length === 0" class="text-gray-500">
      No friend requests.
    </div>
    <div v-else>
      <div v-for="request in friendRequests" :key="request.id" class="flex items-center justify-between p-2 border-b">
        <div>
          <h3 class="font-medium">{{ request.first_name }} {{ request.last_name }}</h3>
          <p class="text-gray-500 text-sm">{{ request.username }}</p>
        </div>
        <div class="flex space-x-2">
          <button @click="handleAccept(request.id)" class="px-3 py-1 bg-green-500 text-white rounded-md">Accept</button>
          <button @click="handleDecline(request.id)" class="px-3 py-1 bg-red-500 text-white rounded-md">Decline</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add any additional styling if needed */
</style>
