<script setup>
import { ref, useId } from 'vue';
import { Link } from '@inertiajs/vue3'

defineProps({
  user: Object,
  isFriend: Number,
});

const FriendshipStatus = {
    NONE: 0,
    REQUEST_PENDING: 1,
    FRIENDSHIP: 2,
    THATS_ME:3,
};

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

        <div>
            <Link
                v-if="isFriend === FriendshipStatus.FRIENDSHIP"
                class="flex items-center space-x-2 cursor-pointer bg-blue"

                href="/unfriend"
                method="post"
                :data="{
                    id: user.id,
                }"
                :only="['isFriend']"
                as="button"
                type="button"
            >
                Unfriend
            </Link>

            <Link
                v-else-if="isFriend === FriendshipStatus.NONE"
                class="flex items-center space-x-2 cursor-pointer bg-blue"

                :href="route('user.friendRequest', user.username)"
                :data="{
                    username: user.username,
                }"
                :only="['isFriend']"
                as="button"
                type="button"
            >
                Add friend
            </Link>

            <label v-else-if="isFriend === FriendshipStatus.REQUEST_PENDING" class="text-gray-400">Friend request sent.</label>

        </div>


        </div>
    </div>

  </div>
</template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
