<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchResultItem from '@/Components/Generic/SearchResultItem.vue';

const { props } = usePage();
const initialQuery = props.initialQuery || '';
const results = ref(props.results || { users: [], groups: [], tags: [] });
const noResults = results.value.users.length === 0 && results.value.groups.length === 0 && results.value.tags.length === 0;
</script>

<template>
    
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto p-6">
            <h2 class="text-2xl font-bold mb-4 text-red-600">
                Search Results for "{{ initialQuery }}" . . .
            </h2>
            

            <!-- Users Section -->
            <div v-if="results.users.length" class="mb-6">
                <h3 class="text-xl font-semibold text-red-400">Users:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <SearchResultItem
                        v-for="user in results.users"
                        :key="`user-${user.id}`"
                        :type="'user'"
                        :image="user.profile_picture"
                        :name="user.username"
                        :id="user.id"
                    />
                </div>
            </div>

            <!-- Groups Section -->
            <div v-if="results.groups.length" class="mb-6">
                <h3 class="text-xl font-semibold text-blue-400">Groups:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <SearchResultItem
                        v-for="group in results.groups"
                        :key="`group-${group.id}`"
                        :type="'group'"
                        :image="group.profile_picture"
                        :name="group.name"
                        :id="group.id"
                    />
                </div>
            </div>

            <!-- Tags Section -->
            <div v-if="results.tags.length" class="mb-6">
                <h3 class="text-xl font-semibold text-green-400">Tags:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <SearchResultItem
                        v-for="tag in results.tags"
                        :key="`tag-${tag.id}`"
                        :type="'tag'"
                        :image="tag.picture || '/default-tag-image.png'"
                        :name="tag.name"
                        :id="tag.id"
                    />
                </div>
            </div>

            <!-- No Results Message -->
            <div v-if="noResults">
                <p class="text-gray-500">No results found.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
