<script setup>
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { ref } from "vue";

library.add(faPlus);

const mostrarModalCrear = ref(false);

const abrirCrearModal = () => {
    mostrarModalCrear.value = true;
};
</script>

<template>
    <button
        @click="abrirCrearModal"
        class="w-full sm:w-auto flex justify-center items-center px-4 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-[#2EBAA1] rounded-lg shadow-md hover:bg-[#288f7c]"
    >
        <font-awesome-icon icon="plus" class="mr-2 text-lg" />
        Nuevo
    </button>
</template>
