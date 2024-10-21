<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Post from '@/Components/Post/Post.vue';

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

      <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="flex flex-wrap justify-between"> <!-- Updated flex properties -->
            <div v-for="post in posts" :key="post.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <Post :post="post" />
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>

