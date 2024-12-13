<script setup>
import { ref, computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import UserListView from '../Generic/UserListView.vue';
import Tag from '../Generic/TagListView.vue';
import CommentsSection from './CommentSection.vue';
import Like from './Like.vue';
import AddTag from './AddTag.vue';
import { Link, usePage,useForm } from '@inertiajs/vue3';

const data = defineProps({
  post: Object, // Expecting the post object as a prop
  viewed_from_group: String, // group name, present if post was viewed though group detail page, used for removing post from group
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


let isEditMenuOpen = ref(false);
const editDescription = () => {
  if (form.content.trim()) {
    data.post.description = form.content;
    isEditMenuOpen.value  = false;
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
const enableDescriptionEdit = () => {
    isEditMenuOpen.value  = !isEditMenuOpen.value ;
};


const deletePost = () => {
  //if (confirm('Are you sure you want to delete this post?')) {
    form.delete(route('post.delete', { post_id: data.post.id }), {
      onSuccess: () => {
        closeOverlay();
      },
      onError: (errors) => {
        console.error('Error deleting the post:', errors);
      },
      preserveScroll: true,
    });
};

const isRollMenuOpen = ref(false);

const toggleRollMenu = () => {
  isRollMenuOpen.value = !isRollMenuOpen.value;
};

const getPostAge = computed(() => {
    if (!data.post.created_at) return '';
  const createdAt = new Date(data.post.created_at);
  const now = new Date();
  const diff = now - createdAt;

  const days = Math.floor(diff / (1000 * 60 * 60 * 24));
  if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;

  const hours = Math.floor(diff / (1000 * 60 * 60));
  if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;

  const minutes = Math.floor(diff / (1000 * 60));
  return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
});

const togglePrivacy = () => {
    data.post.is_public = !data.post.is_public
    form.post(route('post.toggle_is_public', { post_id: data.post.id }), {
        onError: (errors) => {
        console.error('Error toggling post privacy:', errors);
        },
        preserveScroll: true,
    });
};

const removePostFromGroup = () => {
  //if (confirm('Are you sure you want to remove this post from the group?')) {
    form.delete(route('group.post.remove', {
      post_id: data.post.id,
      group_name: data.viewed_from_group
    }), {
      onSuccess: () => {
      },
      onError: (errors) => {
        console.error('Error removing post from group:', errors);
      },
      preserveScroll: true,
    });
};



</script>

<template>
    <div class="post-container ">
      <div class="relative  group">
        <!-- Post Image -->
        <img
          :src="post.photo"
          alt="Post Photo"
          class="post-photo rounded-lg cursor-pointer"
          @click="expandImage(post.photo)"
        />

        <div
          class="absolute top-2 left-2 bg-white bg-opacity-50 p-2 rounded-lg flex items-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
        >
          <UserListView :user="post.owner" />
        </div>
        <!-- Like Button (Bottom-Right Corner) -->
        <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <Like :post="post" />
        </div>
      </div>

      <div v-if="isImageExpanded" class="fixed inset-0 bg-black bg-opacity-75 flex justify-center items-center z-50" @click="closeOverlay">
        <div class="relative w-11/12 max-w-7xl bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg" @click.stop>
          <button class="absolute top-2 right-3 text-white text-3xl" @click="closeOverlay">&times;</button>

          <!-- Roll Menu -->
          <div class="absolute px-0 py-0">
            <button
              v-if="canDelete() || canRemoveFromGroup() || canEdit()"
              class="p-2 rounded-full transition-opacity duration-300 transition-colors duration-300"
              :class="{
                'bg-transparent': !isRollMenuOpen,
                'bg-gray-700 text-white': isRollMenuOpen
              }"
              @click="toggleRollMenu"
            >
              <svg v-if="!isRollMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            <div
              v-if="isRollMenuOpen"
              class="absolute mt-0 left-0 bg-gray-100 bg-opacity-90 dark:bg-gray-700 dark:bg-opacity-90 shadow-md rounded-md py-2 w-48 z-50"
            >
              <button
                v-if="canDelete()"
                class="block w-full text-left px-4 py-2 hover:bg-red-100 dark:hover:bg-red-500 dark:hover:text-white"
                @click="deletePost"
              >
                Delete
              </button>
              <button
                v-if="canRemoveFromGroup()"
                class="block w-full text-left px-4 py-2 hover:bg-red-100 dark:hover:bg-red-500 dark:hover:text-white"
                @click="removePostFromGroup"
              >
                Remove from Group
              </button>
              <button
                v-if="canEdit()"
                class="block w-full text-left px-4 py-2 hover:bg-blue-100 dark:hover:bg-blue-500 dark:hover:text-white"
                @click="togglePrivacy"
              >
                {{ post.is_public ? 'Make Private' : 'Make Public' }}
              </button>
              <button
                v-if="canEdit()"
                class="block w-full text-left px-4 py-2 hover:bg-yellow-100 dark:hover:bg-yellow-500 dark:hover:text-white"
                @click="enableDescriptionEdit"
              >
                Edit Description
              </button>
            </div>
          </div>

          <div class="flex flex-col md:flex-row">
  <!-- Image section -->
  <div class="w-full md:w-3/5">
    <img :src="selectedImage" alt="Expanded Post Photo" class="h-auto max-h-full rounded-lg w-full object-fill" />
  </div>

  <!-- Description -->
  <div class="w-2/5 md:ml-4 mt-4 md:mt-0">
    <div class="image-details mt-4">
      <UserListView :user="post.owner" />

      <p v-if="!isEditMenuOpen" class="text-gray-700 dark:text-gray-300 mt-2">{{ post.description }}</p>
      <div v-if="isEditMenuOpen" class="flex items-center space-x-2 mt-4">
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
        <AddTag v-if="canDelete()" :post_id="post.id" ></AddTag>
        <Tag v-for="tag in post.tags" :tag="tag" :can_delete="canDelete()" />
      </div>

      <p class="text-gray-500 dark:text-gray-400 text-sm">{{ getPostAge }}</p>
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

   /* Image overlay adjustments */
   .post-photo {
     transition: all 0.3s ease-in-out;
   }

   .post-container .group:hover .post-photo {
     transform: scale(1.05); /* Slightly zoom in on image when hovered */
   }

   .group .opacity-0 {
     opacity: 0;
   }

   .group:hover .opacity-0 {
     opacity: 1;
   }

   .fixed {
     display: flex;
     justify-content: center;
     align-items: center;
     z-index: 50;
   }

   .fixed .relative {
     width: 95%;
     max-width: 90hw;
     padding: 1rem;
   }

   .fixed .absolute {
     position: absolute;
   }

   .fixed .shadow-lg {
     box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
   }
   </style>



