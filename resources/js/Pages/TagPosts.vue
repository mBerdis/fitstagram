<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';
import { Link, usePage} from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const data = defineProps({
  tags: Array, // Informácie o tagu
  posts: Object, // Paginované príspevky
});

const { props } = usePage();

const loggedUserRole = computed(() => {
    return props.auth?.user?.role ?? null; // Return null if role is not available
});

const canDelete = () => {
  return loggedUserRole.value !== null && loggedUserRole.value >= 3;
};

const roleOptions = [
  { label: 'Banned', value: 0 },
  { label: 'Silent', value: 1 },
  { label: 'User', value: 2 },
  { label: 'Mod', value: 3 },
  { label: 'Admin', value: 4 },
];

const deleteRoute = () => {
  if (data.tags.length === 1) {
    return `/tag/delete/`;
  } else {
    return `/tags/delete`;
  }
};


</script>

<template>
  <Head :title="`Search results for tag '${tags.join(', ')}'`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Search Results for tag "{{ tags.join(', ') }}"
      </h2>
    </template>

    <div v-if="canDelete()">
      <Link
        v-if="tags.length === 1"
        class="px-3 py-1 bg-red-500 text-white rounded-md"
        :href="route('tag.delete')"
        method="delete"
        :data="{ tags: data.tags }"
        :only="['posts']"
        :preserveScroll="true"
        as="button"
        type="button"
      >
        Delete "{{ tags[0] }}"
      </Link>
      <Link
        v-else
        class="px-3 py-1 bg-red-500 text-white rounded-md"
        :href="route('tags.delete')"
        method="delete"
        :data="{ tags: data.tags }"
        :only="['posts']"
        :preserveScroll="true"
        as="button"
        type="button"
      >
        Delete "{{ tags.join(', ') }}"
      </Link>
    </div>

    <GenericFeed :posts="posts" :viewed_tag="tags"/>

  </AuthenticatedLayout>
</template>
