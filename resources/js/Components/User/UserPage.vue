<script setup>
import { ref } from 'vue';
import Post from '@/Components/Post/Post.vue';
import UserHeader from './UserHeader.vue';
import FriendList from './UserList.vue';
import GenericFeed from '../Generic/GenericFeed.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
  user: Object,
  posts: Array,
  isFriend: Number,
  friends: Array,
  groups: Array,
  friendRequests: Array
});
</script>

<template>
 <div class="">
    <AuthenticatedLayout>
      <template #header>
        <UserHeader :user="user" :isFriend="isFriend" :friends="friends" :groups="groups" :friendRequests="friendRequests"/>
      </template>


      <GenericFeed :posts="posts" :viewed_from_user="user.username" />
    </AuthenticatedLayout>



  </div>
</template>

<style scoped>
</style>
