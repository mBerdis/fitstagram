<script setup>
import { ref } from 'vue';
import { useForm} from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';


const data = defineProps({
    post_id: {
        type: Number,
        required: true
    },
});

const isExpanded = ref(false);
const openOverlay = () => {
  isExpanded.value = true;
};

const closeOverlay = () => {
  isExpanded.value = false;
};

const form = useForm({
    content: '',
    post_id: data.post_id
});

const addTag = () => {
  if (form.content.trim()) {
    form.post(route('post.addTag'), {
      onError: (errors) => {
        console.log('Form submission error:', errors);
      },
      preserveScroll: true
    });
  }
};
</script>

<template>
  <!-- Main button to trigger overlay -->
  <div
    class="card-item flex items-center justify-center px-3 py-1 border rounded-full shadow-sm cursor-pointer bg-gray-100 hover:shadow-md hover:bg-gray-200 transition"
    @click="openOverlay"
  >
    <p class="text-gray-700 text-sm">+</p>
  </div>

  <!-- Overlay with content -->
  <div
    v-if="isExpanded"
    class="fixed inset-0 bg-black bg-opacity-15 flex justify-center items-center z-50"
    @click="closeOverlay"
  >
    <div
      class="relative w-3/4 max-w-4xl bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg"
      @click.stop
    >
        <div class="add-comment flex items-center space-x-2 mt-4">
            <TextInput
                id="content"
                type="text"
                class="block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                v-model="form.content"
                required
                autofocus
                placeholder="Add Tag..."
            />
            <button
                @click="addTag"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Adding...</span>
                <span v-else>add</span>
            </button>
        </div>
    </div>
  </div>
</template>

<style scoped>
/* Optional - Add transition effects for smooth overlay appearance */
</style>
