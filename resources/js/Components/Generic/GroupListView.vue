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

        <Link :href="route('group', group.name)"  class="text-gray-400" >{{ group.name }} </Link>


        <!-- <a @click="goToGroupDetail"  class="flex items-center space-x-2 cursor-pointer">
            <label class="text-gray-400"></label>
        </a> -->
    </div>
</template>
