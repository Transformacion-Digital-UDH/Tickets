<script setup>
import { ref, onMounted, computed } from "vue";
import Table from "../Table.vue";
import ModalCrear from "../ModalCrear.vue";
import ModalVer from "../ModalVer.vue";
import ModalEditar from "../ModalEditar.vue";
import ModalDesactivar from "../ModalDesactivar.vue";
import ModalActivar from "../ModalActivar.vue";
import ButtonNuevo from "../ButtonNuevo.vue";
import axios from "axios";

const categorias = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalDesactivar = ref(false);
const mostrarModalActivar = ref(false);
const itemSeleccionado = ref(null);

const headers = ["N°", "Nombre", "Estado"];

const filtrarCategorias = computed(() => {
    return categorias.value.filter(
        (categoria) =>
            categoria.cat_nombre
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchCategorias = async () => {
    try {
        const response = await axios.get("/categorias");
        categorias.value = response.data.map((categoria) => ({
            id: categoria.id,
            cat_nombre: categoria.cat_nombre,
            cat_activo: categoria.cat_activo,
        }));
    } catch (error) {
        console.error("Error al cargar las categorias:", error);
    }
};

const formFields = [
    { name: "cat_nombre", label: "Nombre", type: "text" },
];

const desactivarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/categorias/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchCategorias();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar la categoría:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/categorias/${itemSeleccionado.value.id}/activar`);
            await fetchCategorias();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al activar la categoría:", error);
        }
    }
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => fetchCategorias());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">Lista de Categorías</h1>

        <div class="flex items-center justify-between mb-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input type="text" v-model="buscarQuery" placeholder="Buscar..."
                    class="flex-grow px-12 pl-10 placeholder-gray-400 border border-gray-300 rounded-md focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5" />
            </div>

            <ButtonNuevo @click="mostrarModalCrear = true" />
        </div>

        <Table :headers="headers" :items="filtrarCategorias" @view="abrirDetallesModal" @edit="abrirEditarModal"
            @activar="abrirActivarModal" @desactivar="abrirDesactivarModal" />

        <ModalCrear v-if="mostrarModalCrear" :formFields="formFields" itemName="Categoría" endpoint="/categorias"
            @cerrar="cerrarCrearModal" @crear="fetchCategorias" />

        <ModalVer v-if="mostrarModalDetalles" :item="itemSeleccionado" itemName="Categoría" :formFields="formFields"
            :mostrarModalDetalles="mostrarModalDetalles" @close="cerrarDetallesModal" />

        <ModalEditar v-if="mostrarModalEditar" :item="itemSeleccionado" itemName="Categoría" :formFields="formFields"
            :mostrarModalEditar="mostrarModalEditar" endpoint="/categorias" @cerrar="cerrarEditarModal"
            @update="fetchCategorias" />

        <ModalDesactivar v-if="mostrarModalDesactivar" :item="itemSeleccionado" itemName="Categoría"
            fieldName="cat_nombre" @cancelar="cerrarDesactivarModal" @confirmar="desactivarItem" />

        <ModalActivar v-if="mostrarModalActivar" :item="itemSeleccionado" itemName="Categoría" fieldName="cat_nombre"
            @cancelar="cerrarActivarModal" @confirmar="activarItem" />
    </div>
</template>
