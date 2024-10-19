<script setup>
import { ref } from 'vue'; // Import ref to handle reactive data
import Author from './Author.vue';
import CommentsSection from './CommentSection.vue';

defineProps({
  post: Object, // Expecting the post object as a prop
});

// Data to control the image overlay
const isImageExpanded = ref(false); // Controls the overlay visibility
const selectedImage = ref(''); // Holds the selected image URL

// Method to handle image click
const expandImage = (imageUrl) => {
  selectedImage.value = imageUrl;
  isImageExpanded.value = true;
};

// Method to close the overlay
const closeOverlay = () => {
  isImageExpanded.value = false;
};
</script>

<template>
 <div class="post-container bg-white dark:bg-gray-800 ">
  <div class="relative inline-block group">
    <!-- Post Image -->
    <img
      :src="post.photo"
      alt="Post Photo"
      class="post-photo rounded-lg cursor-pointer "
      @click="expandImage(post.photo)"
    />

    <div
      class="absolute top-4 left-4 bg-black bg-opacity-50 p-2 rounded-lg flex items-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
    >
      <Author :profilePic="post.owner.profile_picture" :username="post.owner.username" />
    </div>
  </div>



    <!-- Image Overlay (appears when image is clicked) -->
    <div v-if="isImageExpanded" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center z-50">
      <div class="relative w-11/12 max-w-4xl bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg">
        <!-- Close button -->
        <button
          class="absolute top-2 right-2 text-white text-2xl"
          @click="closeOverlay"
        >&times;</button>

        <!-- Expanded image -->
        <img :src="selectedImage" alt="Expanded Post Photo" class="w-full rounded-lg" />

        <!-- Image details and author information -->
        <div class="image-details mt-4">
          <Author :profilePic="post.owner.profile_picture" :username="post.owner.username" />
          <p class="text-gray-700 dark:text-gray-300 mt-2">{{ post.description }}</p>
          <p class="text-gray-500 dark:text-gray-400 text-sm">{{ post.created_at }}</p>
        </div>

        <!-- Comments in the overlay as well -->
        <div class="comments-section mt-4">
          <CommentsSection :post="post" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>


</style>
