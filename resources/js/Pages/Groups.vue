<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/Group/GroupList.vue';
import PopupWindow from '@/Components/Generic/PopupWindow.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AddPhotoIcon from '@/Components/Icons/AddPhotoIcon.vue';

defineProps({
  groups: Array
});

const form = useForm({
    photo: null,
    description: '',
    name: '',
});

const photoPreview = ref(null);
const fileInputRef = ref(null);

// Function to handle file upload and preview
const handlePhotoUpload = (event) => {
    const file = event.target.files[0];
    form.photo = file;

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

const openFileDialog = () => {
    fileInputRef.value.click();
};

const submit = () => {
    form.post(route('group.create'), {
        onFinish: () => form.reset(),
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
    <Head title="Groups" />

    <AuthenticatedLayout>
      <template #header>
        <div class="flex items-center space-x-5">

            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Groups
            </h2>

            <PopupWindow>
            <template #button> New group </template>
                <form @submit.prevent="submit">
                       <!-- Photo Upload or URL -->
                       <div class="mb-4 flex flex-col items-center justify-center">
                            <!-- File Upload Input (hidden) -->
                            <input
                                ref="fileInputRef"
                                type="file"
                                @change="handlePhotoUpload"
                                accept="image/*"
                                class="hidden"
                            />

                            <div class="mt-4 relative items-center justify-center w-28 h-28 rounded-3xl border dark:border-gray-600 overflow-hidden">
                                <!-- Image Preview -->
                                <img v-if="photoPreview" :src="photoPreview" alt="Photo Preview" class="object-cover rounded-3xl" />

                                <!-- Icon Overlay -->
                                <div
                                    v-if="!photoPreview"
                                    class="absolute inset-0 text-gray-500 dark:text-gray-300 flex items-center justify-center bg-gray-100 hover:text-gray-800 cursor-pointer rounded-3xl"
                                    @click="openFileDialog"
                                >
                                    <AddPhotoIcon class="w-28 h-28" />
                                </div>

                                <!-- Positioned "+" Icon over the image when preview exists -->
                                <div
                                    v-if="photoPreview"
                                    class="absolute inset-0 text-white opacity-0 hover:opacity-100 flex items-center justify-center bg-black bg-opacity-20 cursor-pointer"
                                    @click="openFileDialog"
                                >
                                    <AddPhotoIcon class="w-28 h-28" />
                                </div>
                            </div>

                            <InputError class="mt-2" :message="form.errors.photo" />
                        </div>


                    <div>
                        <InputLabel for="name" value="Group name" />

                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="groupname"
                        />

                        <InputError class="mt-2" :message="form.errors.text" />
                    </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea v-model="form.description" class="resize-none w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-gray-200" rows="2"></textarea>
                </div>


                    <div class="mt-4 flex items-center justify-end">
                        <PrimaryButton
                            class="ms-4"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Create group
                        </PrimaryButton>
                    </div>
                </form>
            </PopupWindow>

        </div>
      </template>

      <GroupList :groups="groups"/>

    </AuthenticatedLayout>
  </template>

