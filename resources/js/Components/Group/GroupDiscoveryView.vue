<script setup>
import { Link } from '@inertiajs/vue3';
import GroupListView from '../Generic/GroupListView.vue';

defineProps({
  groups: Array,
});
</script>

<template>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-between mx-10 m-4">
            <div v-for="group in groups" :key="groups.id" class="flex-1 min-w-[400px] mx-2 mb-4">
                <div class="flex items-center space-x-2" mb-1>
                    <Link :href="route('group', group.name)"  class="text-gray-600 flex items-center space-x-2 cursor-pointer" >
                        <img class="w-14 h-14 rounded-full object-cover" :src="group.profile_picture"  />
                        <label class="text-gray-600 text-lg cursor-pointer">{{ group.name }}</label>
                    </Link>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>
</style>
