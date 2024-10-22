<script setup>
import { ref } from 'vue';
import Post from '@/Components/Post/Post.vue';
import UserHeader from './UserHeader.vue';
import FriendList from './FriendList.vue';

defineProps({
  user: Object,
  posts: Array,
});
</script>

<template>
 <div class="">
    <UserHeader :user="user" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="flex flex-wrap justify-between">
            <div v-for="post in posts" class="flex-1 min-w-[400px] mx-2 mb-4">
              <Post :post="post" />
            </div>
          </div>
        </div>
      </div>



  </div>
</template>

<style scoped>
</style>
