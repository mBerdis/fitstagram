<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import UserList from '@/Components/User/UserList.vue';
import GroupList from '@/Components/Group/GroupList.vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';

defineProps({
    group: Object,
    members: Array,
    posts: Array
});
</script>


<template>
    <Head title="Groups"/>

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ group.name }}
        </h2>
      </template>

      <h1 class="text-gray-400">Members</h1>
      <UserList :users="members"></UserList>

      <GenericFeed :posts="posts" />

    </AuthenticatedLayout>
  </template>

