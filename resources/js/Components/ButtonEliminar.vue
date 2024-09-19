<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimes, faTrash } from "@fortawesome/free-solid-svg-icons";

library.add(faTimes, faTrash);
</script>

<template>
    <button
        class="flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-red-400 rounded-lg shadow-md hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
        <font-awesome-icon icon="trash" class="mr-2 text-lg" />
        Eliminar
    </button>
</template>