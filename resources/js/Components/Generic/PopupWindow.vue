<script>

/**
 * Author: Maroš Berdis (xberdi01)
 * Project: Fitstagram (ITU/IIS)
 */

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
      <button
        @click="showContent = !showContent"
        class="px-2 py-2 h-14 text-black flex items-center justify-center rounded-md border border-black hover:bg-black transition duration-500 ease-in-out hover:text-white hover:fill-white"
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
        class="fixed inset-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gray-200 rounded-lg shadow-lg p-6 z-50 max-w-lg w-full h-[400px] overflow-y-auto"
        >
        <h1 class="text-center text-xl">
            <slot name="title"></slot>
        </h1>

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
