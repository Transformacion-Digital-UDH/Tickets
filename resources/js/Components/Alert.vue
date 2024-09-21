<script setup>
defineProps({
    type: {
        type: String,
        default: "success",
    },
    message: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div
        v-if="message"
        :class="[
            'p-4 mb-4 text-sm rounded-lg text-center',
            type === 'success'
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700',
        ]"
        role="alert"
    >
        {{ message }}
    </div>
</template>
