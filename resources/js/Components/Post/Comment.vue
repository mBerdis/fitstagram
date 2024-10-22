<script setup>
import UserListView from '../Generic/UserListView.vue';

defineProps({
  comment: Object,
});
</script>

<template>
  <div class="comment mt-3 flex items-start space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <!-- Author component with user profile picture and username -->
    <UserListView :user="comment.user"/>
    
    <div class="flex-1">
      

      <!-- Display the comment message -->
      <p class="mt-1 text-gray-700 dark:text-gray-300 break-words">
        {{ comment.message }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.comment {
  background-color: rgba(27, 19, 136, 0.1); /* Softer background color */
  border-radius: 8px; /* Rounded corners */
  padding: 10px; /* Padding for better spacing */
}

/* Ensure long words wrap correctly */
.break-words {
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
}

.text-sm {
  font-size: 0.875rem; /* Slightly smaller font for username */
}

.font-semibold {
  font-weight: 600; /* Bolder text for username */
}
</style>
