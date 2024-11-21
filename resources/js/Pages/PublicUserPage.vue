<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';

defineProps({
  user: Object,
  posts: Array,
  isFriend: Number,

  // filled if has access
  friends: Array,
  groups: Array,
  friendRequests: Array
});

</script>


<template>
    <Head :title="user.username" />

    <UserPage :user="user" :posts="posts" :isFriend="isFriend" :friends="friends" :groups="groups" :friendRequests="friendRequests"></UserPage>
  </template>

