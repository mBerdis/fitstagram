<script setup>

defineProps({
    group: {
        type: Object
    }
});


</script>

<template>
    <div class="flex items-center space-x-2" mb-1>
        <a  class="flex items-center space-x-2 cursor-pointer">
            <label class="text-gray-400">{{ group.name }}</label>
        </a>
    </div>
</template>
