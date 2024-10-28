<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserListView from '@/Components/Generic/UserListView.vue';
import GroupListView from '@/Components/Generic/GroupListView.vue';

const { props } = usePage();
const initialQuery = props.initialQuery || '';
const results = ref(props.results || { users: [], groups: [], tags: [] });

</script>



<template>
    <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4 text-yellow-100">Search Results for "{{ initialQuery }}" . . .</h2>

        <div v-if="results">
            <!-- Users Section -->
            <div v-if="results.users.length" class="mb-6 ">
                <h3 class="text-xl font-semibold text-yellow-400">Users:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <UserListView
                        v-for="user in results.users"
                        :key="user.id"
                        :user="user"
                        class="border p-4 rounded-lg shadow-md bg-white dark:bg-gray-800"
                    />
                </div>
            </div>

            <!-- Groups Section -->
            <div v-if="results.groups.length" class="mb-6">
                <h3 class="text-xl font-semibold text-yellow-400">Groups:</h3>
                <div class="space-y-2">
                    <GroupListView
                        v-for="group in results.groups"
                        :key="group.id"
                        :group="group"
                        class="border p-4 rounded-lg shadow-md bg-white dark:bg-gray-800"
                    />
                </div>
            </div>

            <!-- Tags Section -->
            <div v-if="results.tags.length" class="mb-6">
                <h3 class="text-xl font-semibold text-yellow-400">Tags:</h3>
                <div class="space-y-2">
                    <div
                        v-for="tag in results.tags"
                        :key="tag.id"
                        class="border p-4 rounded-lg shadow-md bg-white dark:bg-gray-800 text-gray-400"
                    >
                        {{ tag.name }}
                    </div>
                </div>
            </div>
        </div>

        <div v-else>
            <p class="text-gray-500">No results found.</p>
        </div>
    </div>
</AuthenticatedLayout>

</template>
