<script setup>
/**
 * File: GenericFeed.vue
 * Author: Matej Koscelník (xkosce01)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component represents a feed element displaying posts based on selected criteria.
 *  - Users can sort the posts by "newest" or "rating".
 *  - Pagination controls allow navigation through multiple pages of posts.
 *  - Conditional rendering is used to show delete functionality for tags if the user has appropriate permissions.
 */
import { ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

import Post from '../Post/Post.vue';

const data = defineProps({
    posts: Object,
    viewed_from_group: String,
    group_role: Number,
    viewed_from_user: String,
    viewed_tag: Array,
});

const sortBy = ref('newest');


onMounted(() => {
    const { query } = usePage().props;
    console.log("Aktuálne query:", query);

    if (!query.sort) {
        console.log("neni sort, ukladám predvolený do localStorage");
        localStorage.setItem('sort', savedSort);
        sortBy.value = savedSort;
    } else {
        console.log("existuje sort z URL:", query.sort);
        sortBy.value = query.sort;
        localStorage.setItem('sort', query.sort);
    }
});


const changeSortOrder = (order) => {
    console.log("CHANGED SORT");
    sortBy.value = order;
    localStorage.setItem('sort', order);

    if (data.viewed_from_group) {
        console.log("Vieveed:", data.viewed_from_group);
        router.get(
            route('group', {name: data.viewed_from_group,  sort: order }), // Ensure `order` is passed here
            { only: ['posts'] }
        );
    }
    else if (data.viewed_from_user) {
        router.get(
            route('user', { username: data.viewed_from_user, sort: order }),
            { only: ['posts'] }
        );
    }
    else if (data.viewed_from_myPage) {
        router.get(
            route('user', { username: data.viewed_from_user, sort: order }),
            { only: ['posts'] }
        );
    }
    else if(data.viewed_tag){

        if (data.viewed_tag.length === 1)
        {
            router.get(
                route('tag.posts', { name: data.viewed_tag[0], sort: order }),
                { only: ['posts'] }
            );
        }
        else if (data.viewed_tag.length > 1)
        {
        router.get(
            route('tags.posts', { tags: data.viewed_tag.join('+'), sort: order }),
            { only: ['posts'] }
        );
    }

    }
    else {
        router.get(
            route('feed', { sort: order }),
            { only: ['posts'] }
        );
    }
};

</script>

<template>
  <div class="py-12">
    <!-- Sorting and Posts Container -->
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <!-- Sorting Buttons -->
      <div class="flex items-center bg-gray-100 p-4 rounded-t-lg shadow-md">
        <button
          class="px-4 py-2 text-gray-700 rounded-l-lg focus:outline-none transition-colors"
          :class="sortBy === 'newest' ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'"
          @click="changeSortOrder('newest')"
        >
          Najnovšie
        </button>
        <button
          class="px-4 py-2 text-gray-700 focus:outline-none transition-colors"
          :class="sortBy === 'rating' ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300'"
          @click="changeSortOrder('rating')"
        >
          Podľa hodnotenia
        </button>
      </div>

      <!-- Posts -->
      <div
        class="bg-gray-50 rounded-b-lg p-6 shadow-lg border-t-2 border-blue-500"
        :class="sortBy === 'newest' ? 'border-blue-500' : 'border-blue-700'"
      >
        <div class="flex flex-wrap justify-between">
          <div
            v-for="post in posts.data"
            :key="post.id"
            class="flex-1 min-w-[400px] mx-2 mb-4 bg-white shadow-md rounded-lg p-4"
          >
            <Post :post="post" :viewed_from_group="viewed_from_group" :group_role="group_role" />
          </div>
        </div>

    <!-- Pagination Controls -->
    <div
        :class="{
        'flex justify-between': posts.prev_page_url && posts.next_page_url,
        'flex justify-start': posts.prev_page_url && !posts.next_page_url,
        'flex justify-end': !posts.prev_page_url && posts.next_page_url
    }"
    >
        <Link
            v-if="posts.prev_page_url"
            class="inline-flex items-center rounded-md border border-gray-300 m-2 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
            as="button" type="button"

            :href="posts.prev_page_url"
            :only="['posts']"
        >
            Previous Page
        </Link>

      <Link
            v-if="posts.next_page_url"
            class="inline-flex items-center rounded-md border border-gray-300 m-2 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
            as="button" type="button"

            :href="posts.next_page_url"
            :only="['posts']"
        >
            Next Page
        </Link>

        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
</style>
