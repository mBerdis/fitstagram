<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Props for dynamic data
const data = defineProps({
    tag: {
        type: Object
    }
});

const handleClick = () => {
    router.visit(`/tag/${data.tag.name}`);
};
</script>

<style scoped>
</style>

<template>
    <div
      class="card-item flex items-center justify-center px-3 py-1 border rounded-full shadow-sm cursor-pointer bg-gray-100 hover:shadow-md hover:bg-gray-200 transition"
    @click="handleClick"
   >
        <p class="text-gray-700 text-sm font-medium">{{ data.tag.name }}</p>
    </div>
  </template>


