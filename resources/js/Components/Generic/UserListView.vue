<script setup>

/**
 * Author: Maroš Berdis (xberdi01)
 * Project: Fitstagram (ITU/IIS)
 */

import { Link } from '@inertiajs/vue3'

defineProps({
    user: {
        type: Object
    }
});


</script>

<template>
    <div class="flex items-center space-x-2" v-if="user.profilePic || user.username"  mb-1>
        <Link  class="flex items-center space-x-2 cursor-pointer" :href="route('user',user.username)" >
            <img class="w-9 h-9 rounded-full" :src="user.profile_picture"  />
            <label class="text-gray-600 cursor-pointer">{{ user.username }}</label>
        </Link>
    </div>
  </template>
