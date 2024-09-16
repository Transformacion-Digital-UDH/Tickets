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
    <button @click="abrirCrearModal"
        class="flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gradient-to-r from-green-300 to-[#2EBAA1] rounded-lg shadow-md hover:from-green-400 hover:to-[#2EBAA1]">
        <font-awesome-icon icon="plus" class="mr-2 text-lg" />
        Nuevo
    </button>
</template>