<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GroupList from '@/Components/Group/GroupList.vue';
import PopupWindow from '@/Components/Generic/PopupWindow.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  groups: Array
});

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('group.create'), {
        onFinish: () => form.reset('name'),
    });
};
</script>


<template>
    <Head title="Groups" />

    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Groups
        </h2>
      </template>

      <PopupWindow>
        <template #button> New group </template>

        <form @submit.prevent="submit">
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

      <GroupList :groups="groups"/>

    </AuthenticatedLayout>
  </template>

