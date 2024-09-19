<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faSave, faTimes } from "@fortawesome/free-solid-svg-icons";

library.add(faSave, faTimes);

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    itemName: {
        type: String,
        default: true,
    }
});
</script>

<template>
    <button :disabled="loading"
        class="w-full sm:w-auto flex justify-center items-center px-4 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-[#2EBAA1] rounded-lg shadow-md hover:bg-[#288f7c]">
        <font-awesome-icon v-if="loading" icon="spinner" class="mr-2 animate-spin" />
        <font-awesome-icon v-else icon="save" class="mr-2 text-lg" />
        {{ itemName }}
    </button>
</template>