<script setup>
defineProps({
  comment: Object,
});
</script>

<template>
  <div class="comment mt-2">
    <p class="text-gray-700 dark:text-gray-300">
      <strong>{{ comment.author }}</strong>: {{ comment.content }}
    </p>
  </div>
</template>

<style scoped>
.comment {
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--border-color, #e2e8f0);
}
</style>
