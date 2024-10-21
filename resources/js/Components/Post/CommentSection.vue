<script setup>
import { ref } from 'vue';
import Comment from './Comment.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  post: Object,
});

const user = usePage().props.auth.user;

const form = useForm({
    content: '',
    post_id: props.post.id,
    user: user.id
});



const addComment = () => {
  if (form.content.trim()) {
    form.post(route('comments.store'), {
    onSuccess: () => {
      form.reset();
    },
    onError: (errors) => {
      console.log('Form submission error:', errors);
    },
  });
  }
};

</script>

<template>
  <div class="comments-section mt-2 p-1 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Comments</h3>

    <div v-if="post.comments && post.comments.length > 0" class="space-y-2">
      <div v-for="(comment, index) in post.comments" :key="index" class=" pb-2 mt-2 border-gray-200 dark:border-green-700">
        <Comment :comment="comment" />
      </div>
    </div>
    <p v-else class="text-gray-600 dark:text-gray-400">No comments available.</p>

    <div class="add-comment mt-4">
      <TextInput
        id="content"
        type="text"
        class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        v-model="form.content"
        required
        autofocus
        placeholder="Add a comment..."
      />
      
      <button
        @click="addComment"
        class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
        :disabled="form.processing"
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
