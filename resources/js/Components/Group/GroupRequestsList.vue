<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    join_requests: Array,
});

</script>

<template>
  <div class="max-w-md mx-auto p-4 bg-white rounded-lg shadow-lg">
    <div v-if="join_requests.length === 0" class="text-gray-500">
      No join requests.
    </div>
    <div v-else>
      <div v-for="request in join_requests" :key="request.id" class="flex items-center justify-between p-2 border-b">
        <div>
          <h3 class="font-medium">{{ request.first_name }} {{ request.last_name }}</h3>
          <p class="text-gray-500 text-sm">{{ request.username }}</p>
        </div>
        <div class="flex space-x-2">
            <Link
                class="px-3 py-1 bg-green-500 text-white rounded-md"

                href="/groups/requests/accept"
                method="post"
                :data="{
                    group_id: request.pivot.group_id,
                    user_id: request.pivot.user_id
                }"
                :only="['join_requests', 'members']"
                as="button"
                type="button"
            >
                Accept
            </Link>

            <Link
                class="px-3 py-1 bg-red-500 text-white rounded-md"

                href="/groups/requests/decline"
                method="post"
                :data="{
                    group_id: request.pivot.group_id,
                    user_id: request.pivot.user_id
                }"
                :only="['join_requests']"
                as="button"
                type="button"
            >
                Decline
            </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Add any additional styling if needed */
</style>
