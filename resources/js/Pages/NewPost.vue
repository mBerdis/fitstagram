<script setup>
import InputError from '@/Components/InputError.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue'; // Ensure 'watch' is imported


// Form setup
const form = useForm({
    photo: null,
    photoUrl: '',
    description: '',
    group_ids: [],
    tags: [],
    is_public: true,
});

// Props
defineProps({
    groups: Array,
});

// Preview and photo handling
const useUrl = ref(false);
const photoPreview = ref(null);

const handlePhotoUpload = (event) => {
    const file = event.target.files[0];
    form.photo = file;
    form.photoUrl = '';

    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            photoPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        photoPreview.value = null;
    }
};

const updatePhotoPreview = () => {
    form.photo = null;
    photoPreview.value = form.photoUrl || null;
};

// Dropdown and tags
const dropdownOpen = ref(false);

const closeDropdown = (event) => {
    if (!event.target.closest('.relative')) {
        dropdownOpen.value = false;
    }
};
document.addEventListener('click', closeDropdown);

const newTag = ref('');
const addTag = () => {
    const input = newTag.value.trim();
    if (!input) {
        newTag.value = '';
        return;
    }
    const processedTags = input.split('#')
        .map((tag) => tag.trim().replace(/ /g, '_'))
        .filter((tag) => tag !== '' && !form.tags.includes(tag));
    form.tags.push(...processedTags);
    newTag.value = '';
};

const removeTag = (tagToRemove) => {
    form.tags = form.tags.filter((tag) => tag !== tagToRemove);
};

// Save and load form data from cookies
const saveFormToCookies = () => {
    document.cookie = `postForm=${encodeURIComponent(
        JSON.stringify({
            photoUrl: form.photoUrl,
            description: form.description,
            tags: form.tags,
            group_ids: form.group_ids,
            is_public: form.is_public,
        })
    )}; path=/; max-age=86400`;
};

const loadFormFromCookies = () => {
    const cookies = document.cookie.split('; ');
    const cookie = cookies.find((row) => row.startsWith('postForm='));
    if (cookie) {
        const data = JSON.parse(decodeURIComponent(cookie.split('=')[1]));
        Object.assign(form, data);

        if (data.photoUrl) {
            useUrl.value = true;
            photoPreview.value = data.photoUrl;
        } else {
            useUrl.value = false;
            photoPreview.value = null;
        }
    }
};

// Submit form
const submitPost = () => {
    form.post(route('post.store'), {
        onSuccess: () => {
            form.reset();
            document.cookie = 'postForm=; path=/; max-age=0'; // Clear cookie
        },
        onError: (errors) => {
            console.error('Form submission error:', errors);
        },
    });
};

// Load form data on mount
onMounted(loadFormFromCookies);

// Watch for changes and save to cookies
watch(
    () => ({
        photo: form.photo,
        photoUrl: form.photoUrl,
        description: form.description,
        tags: form.tags,
        group_ids: form.group_ids,
        is_public: form.is_public,
    }),
    saveFormToCookies,
    { deep: true }
);
</script>



<template>
    <Head title="New post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                New post
            </h2>
        </template>

        <div class="max-w-2xl mx-auto mt-8 p-6 bg-white shadow rounded-lg dark:bg-gray-800">
            <form @submit.prevent="submitPost">

                       <!-- Photo Upload or URL -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Photo</label>

                            <!-- Toggle between Upload and URL -->
                            <div class="flex items-center space-x-4 mt-2">
                                <button type="button" @click="useUrl = false" :class="{ 'bg-blue-600 text-white': !useUrl }" class="px-3 py-1 border rounded">
                                    Upload
                                </button>
                                <button type="button" @click="useUrl = true" :class="{ 'bg-blue-600 text-white': useUrl }" class="px-3 py-1 border rounded">
                                    URL
                                </button>
                            </div>

                            <!-- File Upload Input -->
                            <div v-if="!useUrl" class="mt-4">
                                <input type="file" @change="handlePhotoUpload" accept="image/*" class="mt-2 w-full" />
                            </div>

                            <!-- URL Input -->
                            <div v-if="useUrl" class="mt-4">
                                <input type="text" v-model="form.photoUrl" @input="updatePhotoPreview" placeholder="Enter image URL" class="mt-2 w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-gray-200" />
                            </div>

                            <!-- Preview -->
                            <div v-if="photoPreview" class="mt-4">
                                <img :src="photoPreview" alt="Photo Preview" class="w-full h-auto rounded-lg" />
                            </div>

                            <InputError class="mt-2" :message="form.errors.photo" />
                        </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea v-model="form.description" class="mt-2 w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-gray-200" rows="4" placeholder="Write a description..."></textarea>
                </div>

                 <!-- Tags -->
                 <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags</label>
                    <div class="flex items-center space-x-2 mt-4">
                        <input
                            v-model="newTag"
                            type="text"
                            placeholder="Enter a tag..."
                            class="block w-full p-2 border rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-indigo-600"
                        />
                        <button
                            type="button"
                            @click="addTag"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none"
                        >
                            Add
                        </button>
                    </div>
                    <div class="scroll-container mt-4">
                        <div
                            v-for="tag in form.tags"
                            :key="tag"
                            class="flex items-center space-x-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-100 px-3 py-1 rounded-full"
                        >
                            <span>{{ tag }}</span>
                            <button
                                type="button"
                                @click="removeTag(tag)"
                                class="text-red-500 hover:text-red-700"
                            >
                                &times;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Group Selection -->
                <div class="mb-4 relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Groups</label>

                    <div @click="dropdownOpen = !dropdownOpen" class="mt-2 w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-gray-200">
                        <span v-if="form.group_ids.length === 0">Select groups</span>
                        <span v-else>
                            Selected: {{ form.group_ids.map(id => groups.find(group => group.id === id).name).join(', ') }}
                        </span>
                    </div>

                    <div v-show="dropdownOpen" class="mb-4">
                        <div v-for="group in groups" :key="group.id" class="flex items-center p-2 hover:bg-gray-200 dark:hover:bg-gray-700">
                            <input type="checkbox" :value="group.id" v-model="form.group_ids" class="mr-2" />
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ group.name }}</label>
                        </div>
                    </div>
                </div>


                <!-- Visibility Toggle -->
                <div class="mb-4">
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Visibility</span>
                    <div class="flex items-center mt-2 space-x-4">
                        <label class="flex items-center">
                            <input type="radio" v-model="form.is_public" :value="true" />
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Public</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" v-model="form.is_public" :value="false" />
                            <span class="ml-2 text-gray-700 dark:text-gray-300">Private</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create Post
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>

.scroll-container {
  display: flex;
  overflow-x: auto; /* Enable horizontal scrolling */
  padding: 10px 0;
  gap: 8px; /* Space between tags */
  scrollbar-width: thin; /* Firefox scrollbar width */
}

.scroll-container::-webkit-scrollbar {
  height: 6px; /* Scrollbar height */
}

.scroll-container::-webkit-scrollbar-thumb {
  background-color: #cbd5e1; /* Scrollbar thumb color */
  border-radius: 3px;
}

.scroll-container::-webkit-scrollbar-track {
  background-color: #f1f5f9; /* Scrollbar track color */
}

</style>

