<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';
import { Link, usePage } from '@inertiajs/vue3'
import PopupWindow from '@/Components/Generic/PopupWindow.vue';
import GroupRequestsList from '@/Components/Group/GroupRequestsList.vue';
import GroupIcon from '@/Components/Icons/GroupIcon.vue';
import FriendsIcon from '@/Components/Icons/FriendsIcon.vue';
import AddFriendIcon from '@/Components/Icons/AddFriendIcon.vue';
import TrashIcon from '@/Components/Icons/TrashIcon.vue';
import UserListView from '@/Components/Generic/UserListView.vue';

const data = defineProps({
    group: Object,
    members: Array,
    posts: Array,
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

const { props } = usePage();
const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null; // Return null if role is not available
});

const canDelete = () => {
    return (loggedUserRole.value !== null && loggedUserRole.value >= 3)
    || data.membership_status === MembershipStatus.OWNER;
}

</script>


<template>
    <Head title="Groups"/>

    <AuthenticatedLayout>
      <template #header>
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-5">
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
                                class="cursor-pointer bg-gray-600 text-gray-200 rounded p-2"
                                as="button" type="button"

                                href="/groups/leave"
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
                                class="cursor-pointer bg-gray-600 text-gray-200 rounded p-2"
                                as="button" type="button"

                                href="/groups/join"
                                method="post"
                                :data="{
                                    group_id: group.id,
                                }"
                                :only="['membership_status']"
                            >
                                Join
                            </Link>

                            <label v-else-if="membership_status === MembershipStatus.REQUEST_PENDING" class="text-gray-500">Join request sent.</label>
                            <label v-else-if="membership_status === MembershipStatus.OWNER" class="text-gray-500">You own this group.</label>
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
                                        href="/groups/leave"
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
                                href="/groups/delete"
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


      <GenericFeed :posts="posts" :viewed_from_group="group.id" :group_role="membership_status" />

    </AuthenticatedLayout>
  </template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
