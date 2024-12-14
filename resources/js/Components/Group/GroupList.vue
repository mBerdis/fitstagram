<script setup>

/**
 * Author: Maroš Berdis (xberdi01)
 * Project: Fitstagram (ITU/IIS)
 */

import GroupListView from '../Generic/GroupListView.vue';

defineProps({
  groups: Array,
});
</script>

<template>
        <div class="flex-1 min-w-[400px] mx-2 mb-4">
            <div v-for="group in groups" :key="groups.id" class="flex-1 min-w-[400px] mx-2 mb-4">
                    <GroupListView :group="group" />
            </div>
        </div>
</template>

<style scoped>
</style>
