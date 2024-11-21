<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';

const props = defineProps({
  tag: String, // Informácie o tagu
  posts: Object, // Paginované príspevky
});
</script>

<template>
  <Head :title="`Search results for tag '${tag.name}'`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Search Results for tag "{{ tag }}"
      </h2>
    </template>

    <GenericFeed :posts="posts" :viewed_tag="tag"/>

  </AuthenticatedLayout>
</template>
