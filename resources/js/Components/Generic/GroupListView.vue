<script setup>

import { Link } from '@inertiajs/vue3'

defineProps({
    group: {
        type: Object
    }
});

</script>

<template>
    <div class="flex items-center space-x-2" mb-1>
        <Link :href="route('group', group.name)"  class="text-gray-600 flex items-center space-x-2 cursor-pointer" >
            <img class="w-9 h-9 rounded-full object-cover" :src="group.profile_picture"  />
            <label class="text-gray-600 cursor-pointer">{{ group.name }}</label>
        </Link>
    </div>
</template>
