<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';
import UserList from '@/Components/User/UserList.vue';
import GroupList from '@/Components/Group/GroupList.vue';

defineProps({
  groups: Array
});
</script>


<template>
    <Head title="Groups" />

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Groups
        </h2>
      </template>

      <GroupList :groups="groups"/>

    </AuthenticatedLayout>
  </template>

