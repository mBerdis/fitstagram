<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';
import { Link, usePage } from '@inertiajs/vue3'
import PopupWindow from '@/Components/Generic/PopupWindow.vue';
import GroupRequestsList from '@/Components/Group/GroupRequestsList.vue';
import InputError from '@/Components/InputError.vue';
import FriendsIcon from '@/Components/Icons/FriendsIcon.vue';
import AddFriendIcon from '@/Components/Icons/AddFriendIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import UserListView from '@/Components/Generic/UserListView.vue';
import AddPhotoIcon from '@/Components/Icons/AddPhotoIcon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const data = defineProps({
    group: Object,
    members: Array,
    posts: Object,
    membership_status: Number,
    logged_user_id: Number,
    join_requests: Array
});

const MembershipStatus = {
    NONE:              0,
    REQUEST_PENDING:   1,
    MEMBER:            2,
    OWNER:             3,
};

const isEditing = ref(false);
const { props } = usePage();
const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null; // Return null if role is not available
});

const canDelete = () => {
    return (loggedUserRole.value !== null && loggedUserRole.value >= 3)
    || data.membership_status === MembershipStatus.OWNER;
};

const toggleEdit = () => {
    isEditing.value = !isEditing.value;

    form.group_id = data.group.id;
    form.photo = data.group.profile_picture || '';
    form.description = data.group.description || '';
    form.name = data.group.name || '';
};

const form = useForm({
    group_id: data.group.id,
    photo: data.group.profile_picture || '',
    description: data.group.description || '',
    name: data.group.name || '',
});

// Watch for changes in `group` and update form
watch(
    () => data.group,
    (newGroup) => {
        form.description = newGroup.description || '';
        form.name = newGroup.name || '';
    },
    { immediate: true } // Ensures the form is populated on initial load
);

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
    form.post(route('group.update'), {
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
    <Head title="Groups"/>

    <AuthenticatedLayout>
      <template #header>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">

                <div v-if="isEditing" class="flex items-start space-x-5">
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
                    <form @submit.prevent="submit" class="flex-1 space-y-2">
                        <!-- Group Name -->
                        <div>
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="groupname"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Description -->
                        <div>
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="resize-none w-full p- border rounded-lg dark:bg-gray-700 dark:text-gray-200"
                                rows="2"
                                placeholder="Enter group description"
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

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
                                v-if="membership_status === MembershipStatus.OWNER && isEditing"
                                @click="toggleEdit"
                                class="inline-flex items-center rounded-md border border-gray-300 m-2 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"                        >
                            Cancel
                        </button>
                    </form>
                </div>


                <div v-else-if="!isEditing" class="flex items-center space-x-5">
                    <img :src="group.profile_picture" alt="Group Profile Picture" class="w-28 h-28 rounded-full object-cover" />

                    <div class="flex items-center space-x-4">
                        <!-- Member status Buttons -->
                        <div>
                            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 flex items-center space-x-2">
                                {{ group.name }}
                            </h2>

                            <p>
                                {{ group.description }}
                            </p>

                            <Link
                                v-if="membership_status === MembershipStatus.MEMBER"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                                as="button" type="button"
                                :href="route('group.leave', { group_id: group.id, user_id: logged_user_id })"
                                method="post"
                                :data="{
                                    group_id: group.id,
                                    user_id: logged_user_id
                                }"
                                :only="['membership_status']"
                            >
                                Leave
                            </Link>

                            <Link
                                v-else-if="membership_status === MembershipStatus.NONE"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                                as="button" type="button"
                                :href="route('group.join', { group_id: group.id })"
                                method="post"
                                :data="{
                                    group_id: group.id,
                                }"
                                :only="['membership_status']"
                            >
                                Join
                            </Link>

                            <div>
                                <label v-if="membership_status === MembershipStatus.REQUEST_PENDING" class="text-gray-500">Join request sent.</label>
                                <label v-else-if="membership_status === MembershipStatus.OWNER" class="text-gray-500">You own this group.</label>
                            </div>

                            <!-- Toggle Button -->
                            <button
                                v-if="membership_status === MembershipStatus.OWNER && !isEditing"
                                @click="toggleEdit"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:focus:ring-offset-gray-800 dark:active:bg-gray-300"
                            >
                                Edit
                            </button>

                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                        <!-- Popup Buttons -->
                        <div class="flex space-x-4">
                            <PopupWindow v-if="members">
                                <template #button> <FriendsIcon/> </template>
                                <template #title> Members </template>


                                <div v-for="user in members" :key="members.id" class="flex items-center justify-between p-2 border-b">
                                    <UserListView :user="user" />

                                    <Link v-if="canDelete()"
                                        class="px-1 py-1 text-black rounded-md h-11 flex items-center justify-center
                                        border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white"
                                        as="button" type="button"

                                        method="post"
                                        :href="route('group.leave', { group_id: group.id, user_id: user.id })"
                                        :data="{
                                            group_id: group.id,
                                            user_id: user.id,
                                        }"
                                        :only="['members']"
                                    >
                                        <TrashIcon/>
                                    </Link>
                                </div>
                            </PopupWindow>

                            <PopupWindow v-if="join_requests">
                                <template #button> <AddFriendIcon/> </template>
                                <template #title> Join Requests </template>
                                <GroupRequestsList :join_requests="join_requests"/>
                            </PopupWindow>

                            <Link
                                v-if="canDelete()"
                                class="px-2 py-2 h-14 text-black rounded-md flex items-center justify-center
                                border border-black hover:bg-red-500 transition duration-300 ease-in-out hover:text-white"
                                as="button" type="button"

                                method="delete"
                                :href="route('group.delete', { group_id: group.id })"
                                :data="{
                                    group_id: group.id,
                                }"
                            >
                                <TrashIcon/>
                            </Link>
                        </div>
                </div>
        </div>
    </div>

    </template>


      <GenericFeed :posts="posts" :viewed_from_group="group.name" :group_role="membership_status" />

    </AuthenticatedLayout>
  </template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
