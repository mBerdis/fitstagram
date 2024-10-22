<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import UserPage from '@/Components/User/UserPage.vue';

defineProps({
  user: Object,
  posts: Array,
});
</script>


<template>
    <Head title="My Page" />

    <AuthenticatedLayout>

      <UserPage :user="user" :posts="posts"></UserPage>

    </AuthenticatedLayout>
  </template>

