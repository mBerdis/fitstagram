<script setup>
/**
 * File: SearchItemList.vue
 * Author: Filip Halčišák (xhalci02)
 * Project: Fitstagram (ITU/IIS)
 */

import SearchResultItem from '@/Components/Generic/SearchResultItem.vue';



const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    sectionTitle: {
        type: String,
        required: true,
    },
    itemType: {
        type: String,
        required: true,
    },
});
</script>

<template>
  <div v-if="items.length" class="mb-6">
    <h3 class="text-xl font-semibold" :class="{
      'text-red-400': itemType === 'user',
      'text-blue-400': itemType === 'group',
      'text-green-400': itemType === 'tag',
    }">
      {{ sectionTitle }}
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <SearchResultItem
        v-for="item in items"
        :key="`${itemType}-${item.id}`"
        :type="itemType"
        :image="item.profile_picture || item.picture || '/default-tag-image.png'"
        :name="item.username || item.name"
        :id="item.id"
      />
    </div>
  </div>
</template>
