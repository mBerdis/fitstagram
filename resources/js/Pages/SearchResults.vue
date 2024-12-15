<script setup>
/**
 * File: SearchResults.vue
 * Author: Filip Halčišák (xhalci02)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component displays the search results for a given query, categorized into users, groups, and tags.
 *  - Each result is represented as a clickable item, allowing navigation to detailed views.
 */


import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchItemList from '@/Components/Generic/SearchItemList.vue';

const { props } = usePage();
const initialQuery = props.initialQuery || '';
const results = ref(props.results || { users: [], groups: [], tags: [] });
const noResults = results.value.users.length === 0 && results.value.groups.length === 0 && results.value.tags.length === 0;
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6">
      <h2 class="text-2xl font-bold mb-4 text-red-600">
        Search Results for "{{ initialQuery }}" . . .
      </h2>

      <!-- Users Section -->
      <SearchItemList
        :items="results.users"
        section-title="Users"
        item-type="user"
      />

      <!-- Groups Section -->
      <SearchItemList
        :items="results.groups"
        section-title="Groups"
        item-type="group"
      />

      <!-- Tags Section -->
      <SearchItemList
        :items="results.tags"
        section-title="Tags"
        item-type="tag"
      />

      <!-- No Results Message -->
      <div v-if="noResults">
        <p class="text-gray-500">No results found.</p>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
