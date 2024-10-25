<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import UserList from '@/Components/User/UserList.vue';
import GroupList from '@/Components/Group/GroupList.vue';

defineProps({
  user: Object,
  posts: Array,
  friends: Array,
  groups: Array
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

      <h1 class="text-gray-400">Friends</h1>
      <UserList :users="friends" />

      <h1 class="text-gray-400">Groups</h1>
      <GroupList :groups="groups"/>

      <UserPage :user="user" :posts="posts" :isFriend="4"></UserPage>

    </AuthenticatedLayout>
  </template>

