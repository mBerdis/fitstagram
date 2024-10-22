<script setup>

import Post from '../Post/Post.vue';

defineProps({
    posts: {
        type: Array
    }
});

</script>

<template>
      <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="flex flex-wrap justify-between">
            <div v-for="post in posts" :key="post.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <Post :post="post" />
            </div>
          </div>
        </div>
      </div>
</template>
