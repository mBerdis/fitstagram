<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3'
import PopupWindow from '../Generic/PopupWindow.vue';
import UserList from './UserList.vue';
import GroupList from '../Group/GroupList.vue';
import FriendRequestList from './FriendRequestList.vue';
import GroupIcon from '../Icons/GroupIcon.vue';
import FriendsIcon from '../Icons/FriendsIcon.vue';
import AddFriendIcon from '../Icons/AddFriendIcon.vue';
import TrashIcon from '../Icons/TrashIcon.vue';
import UserListView from '../Generic/UserListView.vue';

const data = defineProps({
  user: Object,
  isFriend: Number,
  friends: Array,
  groups: Array,
  friendRequests: Array
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
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-5">
                <!-- User Profile Picture -->
                <img :src="user.profile_picture" alt="Profile Picture" class="w-28 h-28 rounded-full object-cover" />

                <!-- User Info -->
                <div>
                    <h1 class="text-black-300 text-3xl font-bold">{{ user.first_name }} {{ user.last_name }}</h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ user.username }}</p>

                    <!-- Friend Actions -->
                    <div>
                        <Link
                            v-if="isFriend === FriendshipStatus.FRIENDSHIP"
                            class="cursor-pointer bg-gray-600 text-gray-200 rounded p-2"
                            as="button" type="button"

                            method="post"
                            href="/unfriend"
                            :data="{ id: user.id }"
                            :only="['isFriend']"
                        >
                            Unfriend
                        </Link>

                        <Link
                            v-else-if="isFriend === FriendshipStatus.NONE"
                            class="cursor-pointer bg-gray-600 text-gray-200 rounded p-2"
                            as="button" type="button"

                            :href="route('user.friendRequest', user.username)"
                            :data="{ username: user.username }"
                            :only="['isFriend']"
                        >
                            Add friend
                        </Link>

                        <Link
                            v-else-if="isFriend === FriendshipStatus.REQUEST_RECEIVED"
                            class="cursor-pointer bg-gray-600 text-gray-200 rounded p-2"
                            as="button" type="button"

                            method="post"
                            href="/friendRequest/accept"
                            :data="{ id: user.id }"
                            :only="['isFriend']"
                        >
                            Accept Friend Request
                        </Link>

                        <label v-else-if="isFriend === FriendshipStatus.REQUEST_PENDING" class="text-gray-600">Friend request sent.</label>
                    </div>

                    <div v-if="loggedUserRole !== null && loggedUserRole >= 3" class="pt-2">
                        <label for="role-select" class="text-gray-400 font-semibold">User Role:</label>
                        <select id="role-select" v-model="user.role" @change="handleRoleChange(user.role)" class="bg-gray-600 text-white rounded ml-1">
                            <option v-for="option in roleOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </div>

            </div>
        </div>

            <div class="flex items-center space-x-4">
                <!-- Popup Buttons -->
                <div class="flex space-x-4">
                    <PopupWindow v-if="friends">
                        <template #button> <FriendsIcon/> </template>
                        <template #title> Friends </template>

                        <div v-for="user in friends" :key="friends.id" class="flex items-center justify-between p-2 border-b">
                            <UserListView :user="user" />

                            <Link
                                class="px-1 py-1 text-black rounded-md h-11 flex items-center justify-center
                                border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white"
                                as="button" type="button"

                                method="post"
                                href="/unfriend"
                                :data="{
                                    id: user.id,
                                }"
                                :only="['friends']"
                            >
                                <TrashIcon/>
                            </Link>
                        </div>
                    </PopupWindow>

                    <PopupWindow v-if="friendRequests">
                        <template #button> <AddFriendIcon/> </template>
                        <template #title> Friend Requests </template>
                        <FriendRequestList :friendRequests="friendRequests" />
                    </PopupWindow>

                    <PopupWindow v-if="groups">
                        <template #button> <GroupIcon/> </template>
                        <template #title> Groups </template>
                        <GroupList :groups="groups"/>
                    </PopupWindow>

                    <button
                        v-if="loggedUserRole !== null && loggedUserRole >= 4"
                        @click="handleDeleteUser"
                        class="px-2 py-2 h-14 text-black rounded-md flex items-center justify-center
                        border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white">
                        <TrashIcon/>
                    </button>

                </div>
            </div>
        </div>
    </div>

  </div>
</template>

<style scoped>

</style>
