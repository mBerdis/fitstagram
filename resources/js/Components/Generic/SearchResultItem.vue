<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Props for dynamic data
const data = defineProps({
  type: { type: String, required: true },
  image: { type: String, required: true },
  name: { type: String, required: true }, 
  id: { type: Number, required: true }
});

// Handle click event to navigate based on type
const handleClick = () => {
  switch (data.type) {
    case 'user':
        router.visit(route('user', { username: data.name }), {  // Používame pomenovanú routu pre user
            method: 'get',
            replace: false
        });
        break;
    case 'group':
        router.visit(route('group', { name: data.name }), {  // Používame pomenovanú routu pre group
            method: 'get',
            replace: false
        });
        break;
    case 'tag':
        router.visit(route('tag.posts', { name: data.name }), {
            method: 'get', 
            replace: false
        });
        break;
    default:
      console.error('Invalid type provided to CardItem component.');
  }
};


</script>

<style scoped>
.card-item {
  width: 9cm;
  height: 120px;
}
</style>

<template>
    <div 
      class="card-item flex items-center border rounded-2xl shadow-sm cursor-pointer  bg-white hover:shadow-md transition"
      @click="handleClick"
    >
      <!-- Image Section -->
      <div class="image flex-shrink-0 w-1/3 h-full">
        <img 
          :src="image" 
          alt="Image" 
          class="w-full h-full object-cover rounded-tl-2xl rounded-bl-2xl"
        />
      </div>
  
      <!-- Text Section -->
      <div class="text flex-grow text-center pl-4">
        <span class="font-bold text-lg text-gray-800">{{ name }}</span>
      </div>
    </div>
  </template>
  
  
  