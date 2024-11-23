<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const showingNavigationDropdown = ref(false);
const { props } = usePage();
const isAuthenticated = !!props.auth.user; 

const search = ref('');
const history = ref([]);
const historyVisible = ref(false);
const searchForm = ref(null);

const submitSearch = () => {
    if (!search.value || search.value === '#') return;
    const query = search.value.trim();

    if (query.startsWith('#')) {
        const tags = query.split(' ').filter(tag => tag.startsWith('#')).map(tag => tag.slice(1));
        const tagPath = tags.length > 1 ? `/tags/${tags.join('+')}` : `/tag/${tags[0]}`;
        router.visit(tagPath, { method: 'get', data: { query }, replace: false });
    } else {
        router.visit('/search', { method: 'get', data: { query }, replace: false });
    }
    historyVisible.value = false;
};

const selectHistory = (selectedQuery) => {
    search.value = selectedQuery;
    historyVisible.value = false;
    submitSearch();
};

const fetchSearchHistory = async () => {
    if (isAuthenticated) {
        try {
            const response = await axios.get('/search-history');
            history.value = response.data;
            historyVisible.value = true;
        } catch (error) {
            console.error('Error fetching search history:', error);
        }
    }
    
};

const handleClickOutside = (event) => {
    if (searchForm.value && !searchForm.value.contains(event.target)) {
        historyVisible.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});


</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800 w-full">
                <div class="w-full flex h-16 justify-between items-center px-4 sm:px-6 lg:px-8">
                    <!-- Left: Logo and Feed, New Post tabs -->
                    <div class="flex items-center space-x-6">
                        <Link href="/" class="flex items-center">
                            <ApplicationLogo class="block h-10 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            <span class="ml-2 font-bold text-xl text-gray-800 dark:text-gray-200 m-5">Fitstagram</span>
                        </Link>
                        <NavLink :href="route('feed')" :active="route().current('feed')">Feed</NavLink>
                        <span>|</span>
                        <NavLink v-if="isAuthenticated" :href="route('NewPost')" :active="route().current('NewPost')">New Post</NavLink>
                    </div>

                    <!-- Center: Search Bar -->
                <div class="flex-grow flex justify-center">
                    <form @submit.prevent="submitSearch" class="relative w-full m-20" ref="searchForm">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search..."
                            @focus="fetchSearchHistory"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                        />
                        <button
                            type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1111 5a6 6 0 016 6z" />
                            </svg>
                        </button>

                        <!-- Search History Dropdown -->
                        <ul
                            v-if="historyVisible"
                            class="absolute left-0 search-history top-full mt-1 w-full max-h-100 bg-white border border-gray-300 rounded-md shadow-lg overflow-y-auto dark:bg-gray-700 dark:border-gray-600"
                        >
                            <li
                                v-for="item in history"
                                :key="item.id"
                                @click="selectHistory(item.query)"
                                class="px-4 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600"
                            >
                                {{ item.query }}
                            </li>
                        </ul>
                    </form>

                </div>



                    <!-- Right: Profile dropdown or Login/Register tabs -->
                    <div class="flex items-center space-x-6">
                        <NavLink v-if="isAuthenticated" :href="route('MyPage')" :active="route().current('MyPage')">My Page</NavLink>
                        <p  class="font-italic">|</p>
                        <NavLink v-if="isAuthenticated" :href="route('groups')" :active="route().current('groups')">Groups</NavLink>

                        <div v-if="isAuthenticated" class="relative ms-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md ">
                                        <button type="button" class="inline-flex items-center font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                            <span class=" font-bold text-xl text-gray-800 dark:text-gray-200 m-1">{{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }} </span> 
                                            <img :src="props.auth.user.profile_picture" alt="User Photo" class="w-11 h-11 rounded-full"/>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                                </template>
                            </Dropdown>
                        </div>

                        <template v-else>
                            <Link :href="route('login')" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                Log In
                            </Link>
                            <Link :href="route('register')" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ms-2">
                                Register
                            </Link>
                        </template>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>

.search-history {
    z-index: 50;
}
.search-history li {
    transition: background-color 0.2s ease;
}


</style>