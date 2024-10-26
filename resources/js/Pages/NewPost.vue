<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const form = useForm({
    photo: null,
    photoUrl: '',
    description: '',
    group_ids: [],
    is_public: true,
});

defineProps({
  groups: Array
});

const useUrl = ref(false);
const photoPreview = ref(null);

// Function to handle file upload and preview
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

// Function to update preview when URL is used
const updatePhotoPreview = () => {
    form.photo = null;
    photoPreview.value = form.photoUrl || null;
};

const dropdownOpen = ref(false);

// Close dropdown when clicking outside
const closeDropdown = (event) => {
    if (!event.target.closest('.relative')) {
        dropdownOpen.value = false;
    }
};
document.addEventListener('click', closeDropdown);


// Form submission handler
const submitPost = () => {
    form.post(route('post.store'), {
      onSuccess: () => {
        form.reset();
      },
      onError: (errors) => {
        console.log('Form submission error:', errors);
      },
    });
};

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
