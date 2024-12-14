<script setup>

/**
 * Author: Laravel
 * Project: Laravel Breeze Starter Kit used in Fitstagram (ITU/IIS)
 */

defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
