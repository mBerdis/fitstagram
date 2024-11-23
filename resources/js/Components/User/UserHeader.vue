<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router, useForm } from '@inertiajs/vue3'
import PopupWindow from '../Generic/PopupWindow.vue';
import UserList from './UserList.vue';
import GroupList from '../Group/GroupList.vue';
import FriendRequestList from './FriendRequestList.vue';
import GroupIcon from '../Icons/GroupIcon.vue';
import FriendsIcon from '../Icons/FriendsIcon.vue';
import AddFriendIcon from '../Icons/AddFriendIcon.vue';
import TrashIcon from '../Icons/TrashIcon.vue';
import UserListView from '../Generic/UserListView.vue';
import AddPhotoIcon from '../Icons/AddPhotoIcon.vue';
import PrimaryButton from '../PrimaryButton.vue';

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

const isEditing = ref(false);

const toggleEdit = () => {
    isEditing.value = !isEditing.value;

    form.photo = data.user.profile_picture || '';
};

const form = useForm({
    photo: data.user.profile_picture || ''
});

const photoPreview = ref(form.photo);
const fileInputRef = ref(null);

// Function to handle file upload and preview
const handlePhotoUpload = (event) => {
    const file = event.target.files[0];
    form.photo = file;

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        photoPreview.value = null;
    }
};

const openFileDialog = () => {
    fileInputRef.value.click();
};

const submit = () => {
    form.post(route('user.update'), {
        onFinish: () => form.reset(),
        onSuccess: () => {
            form.reset();
            toggleEdit();
        },
        onError: (errors) => {
            console.log('Form submission error:', errors);
        },
    });
};

</script>

<template>
 <div class="">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-5">
                <!-- User Profile Picture -->
                <img v-if="!isEditing" :src="user.profile_picture" alt="Profile Picture" class="w-28 h-28 rounded-full object-cover" />

                <div v-if="isEditing" class="flex items-center space-x-5">
                    <!-- Profile Picture Section -->
                    <div class="relative w-28 h-28">
                        <!-- File Upload Input (hidden) -->
                        <input
                            ref="fileInputRef"
                            type="file"
                            @change="handlePhotoUpload"
                            accept="image/*"
                            class="hidden"
                        />

                        <!-- Image Preview -->
                        <img
                            v-if="photoPreview"
                            :src="photoPreview"
                            alt="Photo Preview"
                            class="w-28 h-28 rounded-full object-cover"
                        />

                        <!-- Positioned "+" Icon over Image Preview -->
                        <div
                            class="absolute inset-0 text-white opacity-60 border-2 border-gray-800 rounded-full hover:opacity-100 flex items-center justify-center bg-black bg-opacity-20 cursor-pointer"
                            @click="openFileDialog"
                        >
                            <AddPhotoIcon class="w-28 h-28" />
                        </div>
                    </div>

                    <!-- Form Section -->
                    <form @submit.prevent="submit" class="flex-1">

                        <h1 class="text-black-300 text-3xl font-bold">{{ user.first_name }} {{ user.last_name }}</h1>
                        <p class="text-gray-600 dark:text-gray-300">{{ user.username }}</p>

                        <!-- Save Button -->
                        <PrimaryButton
                            class="self-end"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Save
                        </PrimaryButton>

                        <!-- Toggle Button -->
                        <button
                                v-if="isFriend === FriendshipStatus.THATS_ME && isEditing"
                                @click="toggleEdit"
                                class="inline-flex items-center rounded-md border border-gray-300 ml-2 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"                        >
                            Cancel
                        </button>
                    </form>
                </div>

                <!-- User Info -->
                <div v-if="!isEditing">
                    <h1 class="text-black-300 text-3xl font-bold">{{ user.first_name }} {{ user.last_name }}</h1>
                    <p class="text-gray-600 dark:text-gray-300">{{ user.username }}</p>

                    <!-- Friend Actions -->
                    <div>
                        <Link
                            v-if="isFriend === FriendshipStatus.FRIENDSHIP"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                            as="button" type="button"

                            method="post"
                            :href="route('unfriend')"
                            :data="{
                                user_id: props.auth?.user?.id,
                                friend_id: user.id,
                            }"
                            :only="['isFriend']"
                        >
                            Unfriend
                        </Link>

                        <Link
                            v-else-if="isFriend === FriendshipStatus.NONE"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                            as="button" type="button"

                            :href="route('user.add.friend', user.username)"
                            :data="{ username: user.username }"
                            :only="['isFriend']"
                        >
                            Add friend
                        </Link>

                        <Link
                            v-else-if="isFriend === FriendshipStatus.REQUEST_RECEIVED"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                            as="button" type="button"

                            :method="'post'"
                            :href="route('friendRequest.accept')"
                            :data="{
                                user_id: props.auth?.user?.id,
                                requestee_id: user.id,
                            }"
                            :only="['isFriend']"
                        >
                            Accept Friend Request
                        </Link>

                        <label v-else-if="isFriend === FriendshipStatus.REQUEST_PENDING" class="text-gray-600">Friend request sent.</label>

                        <!-- Toggle Button -->
                        <button
                                v-if="isFriend === FriendshipStatus.THATS_ME && !isEditing"
                                @click="toggleEdit"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                            >
                                Edit
                        </button>
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
                                :href="route('unfriend')"
                                :data="{
                                    user_id: user.pivot.user1,
                                    friend_id: user.pivot.user2,
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
