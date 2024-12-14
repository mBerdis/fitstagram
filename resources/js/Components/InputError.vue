<script setup>

/**
 * Author: Laravel
 * Project: Laravel Breeze Starter Kit used in Fitstagram (ITU/IIS)
 */

defineProps({
    message: {
        type: String,
    },
});
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600 dark:text-red-400">
            {{ message }}
        </p>
    </div>
</template>
