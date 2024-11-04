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
import GroupIcon from '@/Components/Icons/GroupIcon.vue';
import FriendsIcon from '@/Components/Icons/FriendsIcon.vue';
import AddFriendIcon from '@/Components/Icons/AddFriendIcon.vue';

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
        <div class="max-w-4xl mx-auto p-1">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 flex items-center space-x-2">
                    <GroupIcon/>
                    {{ group.name }}
                </h2>

            <div class="flex items-center space-x-4">
                <!-- Member status Buttons -->
                <div>
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
                </div>

                <!-- Popup Buttons -->
                <div class="flex space-x-4">
                    <PopupWindow v-if="members">
                        <template #button> <FriendsIcon/> </template>
                        <UserList :users="members" />
                    </PopupWindow>

                    <PopupWindow v-if="join_requests">
                        <template #button> <AddFriendIcon/> </template>
                        <GroupRequestsList :join_requests="join_requests"/>
                    </PopupWindow>
                </div>
            </div>

        </div>
    </div>

    </template>


      <GenericFeed :posts="posts" />

    </AuthenticatedLayout>
  </template>

<style scoped>
.bg-blue {
    background-color: #3b82f6;
    padding: 1rem;
}
</style>
