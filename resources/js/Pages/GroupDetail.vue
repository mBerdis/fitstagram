<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import UserList from '@/Components/User/UserList.vue';
import GroupList from '@/Components/Group/GroupList.vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';
import { Link } from '@inertiajs/vue3'
import PopupWindow from '@/Components/Generic/PopupWindow.vue';
import GroupRequestsList from '@/Components/Group/GroupRequestsList.vue';

defineProps({
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

</script>


<template>
    <Head title="Groups"/>

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 flex items-center space-x-2">
            <svg height="2em" class="fill-current text-white" style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M256 73.82c-100.613 0-182.18 81.571-182.18 182.171C73.82 356.6 155.387 438.18 256 438.18c100.608 0 182.18-81.57 182.18-182.188 0-100.608-81.572-182.17-182.18-182.17zM123.294 324.617c0-56.91 49.975-56.9 61.067-71.771l1.274-6.794c-15.583-7.901-26.587-26.939-26.587-49.201 0-29.338 19.081-53.122 42.618-53.122 18.532 0 34.256 14.775 40.122 35.36-23.572 7.487-41.01 33.055-41.01 63.439 0 20.926 8.245 40.148 21.784 52.373-2.175 1.116-4.592 2.25-6.67 3.226-10.921 5.124-26.05 12.235-36.972 26.49zm133.26 45.677h-78.93c0-56.91 49.98-56.891 61.07-71.78l1.27-6.785c-15.582-7.893-26.582-26.93-26.582-49.2 0-29.33 19.081-53.123 42.618-53.123 23.533 0 42.61 23.793 42.61 53.122 0 22.078-10.802 41.01-26.174 49.025l1.442 7.708c12.173 14.16 60.486 15.09 60.486 71.042h-77.81zm76.508-45.677c-10.916-14.247-26.05-21.366-36.966-26.49-2.153-1.002-4.531-2.118-6.662-3.217 13.526-12.234 21.783-31.509 21.783-52.382 0-30.375-17.433-55.943-40.996-63.432 5.862-20.592 21.585-35.367 40.122-35.367 23.537 0 42.61 23.793 42.61 53.122 0 22.078-10.802 41-26.174 49.025l1.44 7.708c12.174 14.159 60.491 15.082 60.491 71.042h-55.647z" data-name="Profile Group Friend"/>
            </svg>

            {{ group.name }}
        </h2>

        <Link
            v-if="membership_status === MembershipStatus.MEMBER"
            class="flex items-center space-x-2 cursor-pointer bg-blue"

            href="/groups/leave"
            method="post"
            :data="{
                group_id: group.id,
                user_id: logged_user_id
            }"
            :only="['membership_status']"
            as="button"
            type="button"
        >
            Leave
        </Link>

        <Link
            v-else-if="membership_status === MembershipStatus.NONE"
            class="flex items-center space-x-2 cursor-pointer bg-blue"

            href="/groups/join"
            method="post"
            :data="{
                group_id: group.id,
            }"
            :only="['membership_status']"
            as="button"
            type="button"
        >
            Join
        </Link>

        <label v-else-if="membership_status === MembershipStatus.REQUEST_PENDING" class="text-gray-400">Join request sent.</label>
        <label v-else-if="membership_status === MembershipStatus.OWNER" class="text-gray-400">You own this group.</label>
    </template>


        <PopupWindow v-if="membership_status >= MembershipStatus.MEMBER">
            <template #button> Members </template>

            <UserList :users="members" />
        </PopupWindow>

        <PopupWindow v-if="membership_status === MembershipStatus.OWNER">
            <template #button> Requests </template>

            <GroupRequestsList :join_requests="join_requests"/>
        </PopupWindow>




      <GenericFeed :posts="posts" />

    </AuthenticatedLayout>
  </template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
