<script setup>
import { ref } from 'vue';
import Comment from './Comment.vue';

defineProps({
  post: Object,
});


const newComment = ref('');

// TODO add comment
const addComment = () => {
  if (newComment.value.trim()) {
    // submit the comment
    alert(`New comment: ${newComment.value}`);
    newComment.value = '';
  }
};
</script>

<template>
  <div class="comments-section mt-4">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Comments</h3>

    <div v-for="(comment, index) in comments" :key="index">
      <Comment :comment="comment" />
    </div>

    <!-- Add new comment form -->
    <div class="add-comment mt-4">
      <textarea
        v-model="newComment"
        class="w-full p-2 border rounded"
        placeholder="Add a comment..."
      ></textarea>
      <button
        @click="addComment"
        class="mt-2 px-4 py-2 bg-blue-600 text-white rounded"
      >
        Submit
      </button>
    </div>
  </div>
</template>

<style scoped>
textarea {
  border: 1px solid var(--border-color, #e2e8f0);
  min-height: 100px;
  resize: none;
}

button {
  cursor: pointer;
}
</style>
