<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import GenericFeed from '@/Components/Generic/GenericFeed.vue';
import { Link, usePage} from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const data = defineProps({
  tags: Array, 
  posts: Object, 
});

const { props } = usePage();

const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null;
});

const canDelete = () => {
  return loggedUserRole.value !== null && loggedUserRole.value >= 3 && !errorMessage;
};

const errorMessage = props.errorMessage || null;


</script>

<template>
  <Head :title="`Search results for tag '${tags.join(', ')}'`" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto p-6">
          <h2 class="text-2xl font-bold mb-4 text-red-600">
            Search Results for tag "{{ tags.join(', ') }}" . . .
          </h2>
        

        <div v-if="errorMessage" >
          <p class="text-gray-500">{{ errorMessage }} </p>
          
        </div>

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

        <GenericFeed v-if="!errorMessage" :posts="posts" :viewed_tag="tags" />
    </div>        
  </AuthenticatedLayout>
</template>
