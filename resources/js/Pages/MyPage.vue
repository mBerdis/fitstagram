<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import FriendList from '@/Components/User/FriendList.vue';

defineProps({
  user: Object,
  posts: Array,
  friends: Array
});
</script>


<template>
    <Head title="My Page" />

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          My Page
        </h2>
      </template>

      <FriendList :friends="friends" />

      <UserPage :user="user" :posts="posts"></UserPage>

    </AuthenticatedLayout>
  </template>

