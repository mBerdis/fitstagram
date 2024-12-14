<script setup>
/**
 * File: Comment.vue
 * Author: Matej Koscelník (xkosce01)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component represents a single comment with user details and content.
 *  - Provides delete functionality for authorized users (comment owner or roles >= Moderator).
 */
import { ref, computed } from 'vue';
import UserListView from '../Generic/UserListView.vue';
import { Link, usePage } from '@inertiajs/vue3';

const data = defineProps({
  comment: Object,
});

const { props } = usePage();
const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null; // Return null if role is not available
});

const canDelete = () => {
    console.log(loggedUserRole);
    return (loggedUserRole.value !== null && loggedUserRole.value >= 3)
    || data.comment.user_id === props.auth?.user?.id;
}

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
    <Link v-if="canDelete()"
            class="text-gray-700 ml-1 text-xl leading-none relative hover:text-red-500 transition"

            :href="route('comment.delete', { id: comment.id })"
            method="delete"
            :data="{
                comment_id: comment.id
            }"

            :preserveScroll="true"
            as="button"
            type="button"
        >
        <b>&times;</b>
    </Link>
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
