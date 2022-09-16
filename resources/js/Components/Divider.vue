<template>
    <div class="relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-300" />
        </div>
        <div class="relative flex justify-center">
            <span class="px-2 bg-white text-sm text-gray-500" v-text="message"></span>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({ 'message': String })
</script>
