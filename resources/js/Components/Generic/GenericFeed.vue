<script setup>

import Post from '../Post/Post.vue';

defineProps({
    posts: Array,
    viewed_from_group: Number, // group ID, present if post was viewed though group detail page, used for removing post from group
    group_role: Number         // group role, present if post was viewed though group detail page, used for removing post from group
});


</script>

<template>
      <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="flex flex-wrap justify-between">
            <div v-for="post in posts" :key="post.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <Post :post="post" :viewed_from_group="viewed_from_group" :group_role="group_role" />
            </div>
          </div>
        </div>
      </div>
</template>
