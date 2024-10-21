<script setup>
import Author from './Author.vue';

defineProps({
  comment: Object,
});
</script>

<template>
  <div class="comment mt-3 flex items-start space-x-4">
    <!-- Use the Author component to display the user's profile picture and username -->
    <Author :profilePic="comment.user.profile_picture" :username="comment.user.username" />
    
    <!-- Add a colon after the Author component -->
    <span class="mt-1 text-gray-700 dark:text-gray-300">:</span>

    <!-- Display the comment message -->
    <div class="flex-1">
      <p class="text-gray-700 dark:text-gray-300 break-words mt-1">
        {{ comment.message }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.comment {
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-color, #e2e8f0);
}

/* Ensure long words wrap correctly */
.break-words {
  overflow-wrap: break-word; /* For long words */
  word-wrap: break-word; /* Older browsers */
  word-break: break-word; /* Ensure breaking of long words */
}
</style>
