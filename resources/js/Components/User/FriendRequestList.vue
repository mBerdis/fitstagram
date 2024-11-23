<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserListView from '../Generic/UserListView.vue';
import AcceptIcon from '../Icons/AcceptIcon.vue';
import CancelIcon from '../Icons/CancelIcon.vue';

const props = defineProps({
  friendRequests: Array,
});

</script>

<template>
<div v-if="friendRequests.length === 0" class="text-gray-500">
      No friend requests.
    </div>
    <div v-else>
      <div v-for="request in friendRequests" :key="request.id" class="flex items-center justify-between p-2 border-b">
        <UserListView :user="request" />

        <div class="flex space-x-3">
            <Link
                class="px-2 py-0.5 text-black rounded-md h-11 flex items-center justify-center
                border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white"

                :href="route('friendRequest.decline')"
                :method="'post'"
                :data="{
                    user_id: request.pivot.user2,
                    requestee_id: request.pivot.user1,
                }"
                :only="['friendRequests']"
                as="button"
                type="button"
            >
                <CancelIcon/>
            </Link>

            <Link
                class="px-2 py-0.5 text-black rounded-md h-11 flex items-center justify-center
                border border-black hover:bg-green-500 transition duration-300 ease-in-out hover:text-white hover:fill-green-500"

                :href="route('friendRequest.accept')"
                :method="'post'"
                :data="{
                    user_id: request.pivot.user2,
                    requestee_id: request.pivot.user1,
                }"
                :only="['friendRequests', 'friends']"
                as="button"
                type="button"
            >
                <AcceptIcon/>
            </Link>
        </div>
      </div>
    </div>
</template>

<style scoped>
</style>
