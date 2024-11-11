<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import UserList from '@/Components/User/UserList.vue';
import GroupList from '@/Components/Group/GroupList.vue';
import FriendRequestList from '@/Components/User/FriendRequestList.vue';

defineProps({
  user: Object,
  posts: Array,
  friends: Array,
  groups: Array,
  friendRequests: Array
});
</script>


<template>
    <Head title="My Page" />

    <UserPage :user="user" :posts="posts" :isFriend="4" :friends="friends" :groups="groups" :friendRequests="friendRequests"></UserPage>
  </template>

