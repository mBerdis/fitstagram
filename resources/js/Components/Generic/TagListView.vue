<script setup>
import { ref } from 'vue';
import { router,Link } from '@inertiajs/vue3';


const data = defineProps({
    tag: {
        type: Object
    },
    can_delete: {
        type: Boolean
    },
});

const handleClick = () => {
    router.visit(route('tag.posts', { name: data.tag.name }), {
        method: 'get', // Optional: GET is the default HTTP method
    });
};

</script>

<style scoped>
</style>

<template>
    <div
      class="card-item flex items-center justify-center px-3 py-1 border rounded-full shadow-sm cursor-pointer bg-gray-100 hover:shadow-md hover:bg-gray-200 transition"
    @click="handleClick"
   >
        <p class="text-gray-700 text-sm font-medium">{{ data.tag.name }}</p>
        <Link
            v-if="can_delete"
            class="text-gray-700 ml-1 text-lg leading-none relative hover:text-red-500 transition"
            :href="route('post.delete_tag', { post_id: data.tag.pivot.post_id, tag_id: data.tag.id })"
            as="button"
            method="post"
            :data="{ post_id: data.tag.pivot.post_id, tag_id: data.tag.id }"
            :preserve-scroll="true"
            @click.stop
        >
            &times;
        </Link>
    </div>
  </template>


