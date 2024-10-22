<script setup>
import { ref } from 'vue';
import GroupListView from '../Generic/GroupListView.vue';

defineProps({
  groups: Array,
});
</script>

<template>
 <div class="">

    <div v-for="group in groups" :key="groups.id" class="flex-1 min-w-[400px] mx-2 mb-4">
              <GroupListView :group="group" />
    </div>

  </div>
</template>

<style scoped>
</style>
