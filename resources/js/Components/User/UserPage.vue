<script setup>
import { ref } from 'vue';
import Post from '@/Components/Post/Post.vue';
import UserHeader from './UserHeader.vue';
import FriendList from './UserList.vue';
import GenericFeed from '../Generic/GenericFeed.vue';

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
    <UserHeader :user="user" :isFriend="isFriend" :friends="friends" :groups="groups" :friendRequests="friendRequests"/>

    <GenericFeed :posts="posts" />

  </div>
</template>

<style scoped>
</style>
