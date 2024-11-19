<script setup>
import { ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3'; // Import Link

// Props
const props = defineProps({
  post: {
    type: Object,
  },
});

// Reactive State
const liked = ref(props.post.liked_by_user); // Initialize with prop value

// Watch for Prop Updates
watch(
  () => props.post.liked_by_user,
  (newValue) => {
    liked.value = newValue; // Update local state if prop changes
  }
);

// Toggle Like State Locally
function toggleLike(event) {
  event.preventDefault();

  // Optimistic UI Update
  liked.value = !liked.value;
}
</script>

<template>
  <div class="flex items-center space-x-2">
    <!-- Like Button using <Link> -->
    <Link
      class="px-3 py-1 rounded-md"
      :class="liked ? 'bg-red-500 text-white' : 'bg-gray-200 text-gray-700'"
      href="/post/toggle_like"
      method="post"
      :data="{ post_id: props.post.id }"
      :preserve-scroll="true"
      as="button"
      @click="toggleLike"
    >
      <span>
        <i class="fa" :class="liked ? 'fa-heart text-red-500' : 'fa-heart-o text-gray-500'"></i>
      </span>
    </Link>
  </div>
</template>

<style scoped>
/* Optional styling */
button {
  transition: background-color 0.3s ease, color 0.3s ease;
}
</style>
