<script>
export default {
  data() {
    return {
        showContent: false,  // Controls the visibility of the user list
    };
  },
};

</script>


<template>
    <div>
      <!-- Toggle Button -->
      <button
        @click="showContent = !showContent"
        class="px-4 py-2 bg-blue-500 text-white rounded-md"
      >
        <slot name="button">Show Content</slot> <!-- Use named slot for button text -->
      </button>

      <div
        v-if="showContent"
        class="fixed inset-0 bg-black bg-opacity-50 z-40"
        @click="showContent = false"
      ></div>

      <div
        v-if="showContent"
        class="fixed inset-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-sky-900 rounded-lg shadow-lg p-6 z-50 max-w-lg w-full h-[400px] overflow-y-auto"
        >
        <slot />
      </div>

    </div>
  </template>


<style scoped>
.fixed {
  position: fixed;
}

.z-40 {
  z-index: 40;
}

.z-50 {
  z-index: 50;
}
</style>
