<script setup>
/**
 * File: Like.vue
 * Author: Matej Koscelník (xkosce01)
 * Project: Fitstagram (ITU/IIS)
 * Description:
 *  - This component provides a like button for posts.
 *  - The button toggles the like status visually and via the backend.
 *  - Requires a authenticated user to toggle likes.
 */
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  post: {
    type: Object,
  },
  userRole:{
    type: Number
  }
});


const liked = ref(props.post.liked_by_user);

watch(
  () => props.post.liked_by_user,
  (newValue) => {
    liked.value = newValue;
  }
);


function toggleLike(event) {
  event.preventDefault();
  if (props.userRole >= 2){
    liked.value = !liked.value;
  }


}
</script>

<template>
  <div class="flex items-center space-x-2">
    <Link
      class="px-1 py-0.5 rounded-md"
      :class="liked ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'"
    :href="route('post.toggle_like', { post_id: props.post.id })"
      method="post"
      :data="{ post_id: props.post.id }"
      :preserve-scroll="true"
      as="button"
      @click="toggleLike"
    >
        <!-- SVG Heart Icon -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-5 h-5"
        fill="currentColor"
        viewBox="0 0 24 24"
        :class="liked ? 'text-white' : 'text-gray-500'"
      >
        <path
          :d="liked
            ? 'M12 20l5-3c2-2 4-3 4-8 0-2-1-4-4-4-4 0-4 3-5 4-1-1-1-4-5-4C4 5 3 7 3 9c0 5 2 6 4 8l5 3Z'
            : 'M12 20l5-3c2-2 4-3 4-8 0-2-1-4-4-4-4 0-4 3-5 4-1-1-1-4-5-4C4 5 3 7 3 9c0 5 2 6 4 8l5 3Z'"
        />
      </svg>
    </Link>
  </div>
</template>

<style scoped>
</style>
