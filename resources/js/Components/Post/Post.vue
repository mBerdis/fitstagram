<script setup>
import { ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import UserListView from '../Generic/UserListView.vue';
import Tag from '../Generic/TagListView.vue';
import CommentsSection from './CommentSection.vue';
import Like from './Like.vue';
import { Link, usePage,useForm } from '@inertiajs/vue3';

const data = defineProps({
  post: Object, // Expecting the post object as a prop
  viewed_from_group: Number, // group ID, present if post was viewed though group detail page, used for removing post from group
  group_role: Number         // group role, present if post was viewed though group detail page, used for removing post from group
});


const MembershipStatus = {
    NONE:              0,
    REQUEST_PENDING:   1,
    MEMBER:            2,
    OWNER:             3,
};

const { props } = usePage();
const loggedUserRole = computed(() => {
  return props.auth?.user?.role ?? null; // Return null if role is not available
});

const roleOptions = [
  { label: 'Banned', value: 0 },
  { label: 'Silent', value: 1 },
  { label: 'User', value: 2 },
  { label: 'Mod', value: 3 },
  { label: 'Admin', value: 4 },
];

// Data to control the image overlay
const isImageExpanded = ref(false); // Controls the overlay visibility
const selectedImage = ref(''); // Holds the selected image URL

// Method to handle image click
const expandImage = (imageUrl) => {
  selectedImage.value = imageUrl;
  isImageExpanded.value = true;
};

// Method to close the overlay
const closeOverlay = () => {
  isImageExpanded.value = false;
};

const canDelete = () => {
    return (loggedUserRole.value !== null && loggedUserRole.value >= 3)
    || data.post.user_id === props.auth?.user?.id;
}

const canEdit = () => {
    return data.post.user_id === props.auth?.user?.id;
}

const canRemoveFromGroup = () => {
    const isViewedFromGroup = data.viewed_from_group !== undefined;
    const isGroupOwner = data.group_role === MembershipStatus.OWNER;
    const isPostOwner = data.post.user_id === props.auth?.user?.id;
    const isAtLeastMod = loggedUserRole.value !== null && loggedUserRole.value >= 3;

    return isViewedFromGroup && (isGroupOwner || isPostOwner || isAtLeastMod);
}

let form;
if (!!usePage().props.auth.user) {
  const user = usePage().props.auth.user;

  form = useForm({
    content: data.post.description,
    post_id: data.post.id
  });
}

const editDescription = () => {
  if (form.content.trim()) {
    form.post(route('post.editDescription'), {
      onSuccess: () => {
        form = useForm({
            content: data.post.description,
            post_id: data.post.id
        });
      },
      onError: (errors) => {
        console.log('Form submission error:', errors);
      },

      preserveScroll: true
    });
  }
};



</script>

<template>
 <div class="post-container bg-white dark:bg-gray-800 ">
  <div class="relative inline-block group">
    <!-- Post Image -->
    <img
      :src="post.photo"
      alt="Post Photo"
      class="post-photo rounded-lg cursor-pointer "
      @click="expandImage(post.photo)"
    />

    <div
      class="absolute top-4 left-4 bg-black bg-opacity-50 p-2 rounded-lg flex items-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
    >
      <UserListView :user="post.owner" />
    </div>
    <!-- Like Button (Bottom-Right Corner) -->
    <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        <Like :post="post" />
      </div>
  </div>



  <div v-if="isImageExpanded" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center z-50" @click="closeOverlay">
  <div class="relative w-11/12 max-w-7xl  bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg" @click.stop>
    <button class="absolute top-2 right-3 text-white text-3xl" @click="closeOverlay">&times;</button>

    <Link v-if="canDelete()"
            class="px-3 py-1 bg-red-500 text-white rounded-md"

            href="/post/delete"
            method="delete"
            :data="{
                post_id: post.id
            }"
            :only="['posts']"
            :preserveScroll="true"
            as="button"
            type="button"
        >
            Delete
    </Link>

    <Link v-if="canRemoveFromGroup()"
            class="px-3 py-1 bg-red-500 text-white rounded-md"

            href="/groups/post/remove"
            method="delete"
            :data="{
                post_id: post.id,
                group_id: viewed_from_group
            }"
            :only="['posts']"
            :preserveScroll="true"
            as="button"
            type="button"
        >
            Remove from group
    </Link>

    <div class="flex flex-col md:flex-row">
      <!-- Image section -->
      <div class="">
        <img :src="selectedImage" alt="Expanded Post Photo" class="w-full rounded-lg" />
      </div>

       <!-- Description -->
      <div class="w-full md:w-1/2 md:ml-4 mt-4 md:mt-0">
        <div class="image-details mt-4">
            <UserListView :user="post.owner" />

            <p v-if="!canEdit()" class="text-gray-700 dark:text-gray-300 mt-2">{{ post.description }}</p>
            <div v-if="canEdit()" class="flex items-center space-x-2 mt-4">
                <TextInput
                id="editableDescription"
                type="text"
                class="block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                v-model="form.content"
                required
                autofocus
                />

                <button
                @click="editDescription"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600"
                :disabled="form.processing"
                >
                    Save
                </button>
            </div>

            <div class="scroll-container">
                <Tag v-for="tag in post.tags" :tag="tag" :can_delete=canDelete() />
            </div>

            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ post.created_at }}</p>
            <div class="absolute top-4 right-4 flex items-center space-x-2">
                <p class="text-gray-700 dark:text-gray-300 mt-2">{{ post.like_count }}</p>
                <Like :post="post" />
            </div>
        </div>
        <!-- Comments section -->
        <div class="comments-section">
          <CommentsSection :post="post" />
        </div>
      </div>
    </div>
  </div>
</div>

  </div>
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
