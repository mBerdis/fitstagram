<script setup>
/**
 * File: Feed.vue
 * Author: Matej Koscelník (xkosce01)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component represents a feed of posts
 */
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Post from '@/Components/Post/Post.vue';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';

const props = defineProps({
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

