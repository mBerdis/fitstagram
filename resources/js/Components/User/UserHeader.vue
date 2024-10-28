<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'

const data = defineProps({
  user: Object,
  isFriend: Number,
});

const FriendshipStatus = {
    NONE:              0,
    REQUEST_PENDING:   1,
    FRIENDSHIP:        2,
    REQUEST_RECEIVED:  3,
    THATS_ME:          4,
};

const { props } = usePage();
const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null; // Return null if role is not available
});

const roleOptions = [
  { label: 'Banned', value: 0 },
  { label: 'Silent', value: 1 },
  { label: 'User', value: 2 },
  { label: 'Mod', value: 3 },
  { label: 'Admin', value: 4 },
];

function handleRoleChange(newRole) {
    router.post(route('user.updateRole'), { role: newRole , id: data.user.id });
}

function handleDeleteUser() {
    router.post(route('user.delete'), { id: data.user.id });
}

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

            <Link
                v-else-if="isFriend === FriendshipStatus.REQUEST_RECEIVED"
                class="flex items-center space-x-2 cursor-pointer bg-blue"
                href="/friendRequest/accept"
                method="post"
                :data="{ id: user.id }"
                :only="['isFriend']"
                as="button"
                type="button"
            >
                Accept Friend Request
            </Link>

            <label v-else-if="isFriend === FriendshipStatus.REQUEST_PENDING" class="text-gray-400">Friend request sent.</label>

        </div>


        </div>
    </div>

    <div v-if="loggedUserRole !== null && loggedUserRole >= 3" class="mt-4">
        <label for="role-select" class="text-gray-400 font-semibold">Change User Role:</label>
        <select id="role-select" v-model="user.role" @change="handleRoleChange(user.role)" class="bg-gray-700 text-white rounded p-2 mt-2">
            <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
    </div>

    <!-- Delete User Button -->
    <div v-if="loggedUserRole !== null && loggedUserRole >= 4" class="mt-4">
        <button @click="handleDeleteUser" class="bg-red-600 text-white rounded p-2">
            Delete User
        </button>
    </div>

  </div>
</template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
