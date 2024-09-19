<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faSpinner, faTimes } from "@fortawesome/free-solid-svg-icons";

library.add(faSpinner, faTimes);
</script>

<template>
    <button
        class="flex justify-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gray-400 rounded-lg shadow-md hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
        <font-awesome-icon icon="times" class="mr-2 text-lg" />
        Cerrar
    </button>
</template>