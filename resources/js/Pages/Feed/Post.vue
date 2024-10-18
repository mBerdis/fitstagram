<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Author from '@/Components/Post/Author.vue';

defineProps({
    posts: Object,
  });

</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Feed
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                >
                    <div v-for="post in posts":key="post.id">
                        <Author
                            :profilePic="post.owner.profile_picture"
                            :username="post.owner.username"
                        ></Author>

                        <!-- Display the post photo if available -->
                        <div v-if="post.photo">
                            <img :src="post.photo" alt="Post Photo" class="post-photo">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
