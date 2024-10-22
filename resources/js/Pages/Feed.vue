<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Post from '@/Components/Post/Post.vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';

defineProps({
    posts: Object,
  });

</script>


<template>
    <Head title="feed" />

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Feed
        </h2>
      </template>

      <GenericFeed :posts="posts" />

    </AuthenticatedLayout>
  </template>

