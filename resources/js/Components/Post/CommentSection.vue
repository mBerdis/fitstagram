<script setup>
/**
 * File: CommentSection.vue
 * Author: Matej Koscelník (xkosce01)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component renders a comment section for a post.
 *  - Displays all comments with a scrollable interface.
 *  - Allows authenticated users to add comments.
 */
import { ref } from 'vue';
import Comment from './Comment.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  post: Object,
});

const isAuthenticated = !!usePage().props.auth.user;

let form;
if (isAuthenticated) {
  const user = usePage().props.auth.user;

  form = useForm({
    content: '',
    post_id: props.post.id,
    user: user.id,
  });
}

const addComment = () => {
  if (form.content.trim()) {
    form.post(route('comments.store'), {
      onSuccess: () => {
        form.reset();
      },
      onError: (errors) => {
        console.log('Form submission error:', errors);
      },

      preserveScroll: true
    });
  }
};
</script>

<template>
  <div class="comments-section mt-2 p-4 bg-white rounded-lg shadow-md dark:bg-gray-800 h-full flex flex-col">
    <!-- <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Comments</h3>

     Scrollable comments container -->
    <div v-if="post.comments && post.comments.length > 0" class="comments-container flex-1 overflow-y-auto custom-scrollbar space-y-2">
      <div v-for="(comment, index) in [...post.comments].reverse()" :key="index" class="pb-0 mt-2 border-b border-gray-200 dark:border-gray-600">
        <Comment :comment="comment" />
      </div>
    </div>

    <p v-else class="text-gray-600 dark:text-gray-400 flex-1 overflow-hidden">No comments available.</p>

    <!-- Add comment section, remains fixed after the scrollable comments -->
    <div v-if="isAuthenticated" class="add-comment flex items-center space-x-2 mt-4">
      <TextInput
        id="content"
        type="text"
        class="block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        v-model="form.content"
        required
        autofocus
        placeholder="Add a comment..."
      />

      <button
        @click="addComment"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
        :disabled="form.processing"
      >
        Submit
      </button>
    </div>
  </div>
</template>

<style scoped>
  /* Scrollable comments section */
  .comments-section {
    height: 315px; /* Set the fixed height of the comments section */
  }

  .comments-container {
    max-height: 100%; /* Allow the comments to fill the available space and scroll */
  }

  .custom-scrollbar::-webkit-scrollbar {
    width: 10px;
  }

  .custom-scrollbar::-webkit-scrollbar-track {
    background: transparent; /* Make the track transparent */
  }

  .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #3B82F6;
    border-radius: 10px; /* Rounded ends */
  }

  button {
    cursor: pointer;
  }
</style>
