<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserListView from '../Generic/UserListView.vue';
import CancelIcon from '../Icons/CancelIcon.vue';
import AcceptIcon from '../Icons/AcceptIcon.vue';

const props = defineProps({
    join_requests: Array,
});

</script>

<template>
<div v-if="join_requests.length === 0" class="text-gray-500">
      No join requests.
    </div>
    <div v-else>
      <div v-for="request in join_requests" :key="request.id" class="flex items-center justify-between p-2 border-b">
        <UserListView :user="request" />

        <div class="flex space-x-2">
            <Link
                class="px-2 py-0.5 text-black rounded-md h-11 flex items-center justify-center
                border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white"
                as="button" type="button"

                method="post"
                :href="route('group.request.decline', { group_id: request.pivot.group_id, user_id: request.pivot.user_id })"
                :data="{
                    group_id: request.pivot.group_id,
                    user_id: request.pivot.user_id
                }"
                :only="['join_requests']"
            >
                <CancelIcon/>
            </Link>

            <Link
                class="px-2 py-0.5 text-black rounded-md h-11 flex items-center justify-center
                border border-black hover:bg-green-500 transition duration-300 ease-in-out hover:text-white hover:fill-green-500"
                as="button" type="button"

                method="post"
                :href="route('group.request.accept', { group_id: request.pivot.group_id, user_id: request.pivot.user_id })"
                :data="{
                    group_id: request.pivot.group_id,
                    user_id: request.pivot.user_id
                }"
                :only="['join_requests', 'members']"
            >
                <AcceptIcon/>
            </Link>
        </div>
      </div>
    </div>
</template>

<style scoped>
/* Add any additional styling if needed */
</style>
