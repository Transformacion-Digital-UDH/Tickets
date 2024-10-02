<script setup>
import { ref, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Table from "@/Components/Table.vue";
import ModalCrear from "@/Components/ModalCrear.vue";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";
import ButtonNuevo from "@/Components/ButtonNuevo.vue";
import axios from "axios";

const categorias = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);

const headers = ["N°", "Nombre", "Estado"];

const filtrarCategorias = computed(() => {
    return categorias.value.filter((categoria) =>
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
        toast.error("Error al cargar los categorías", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

const formFields = [
    { name: "cat_nombre", label: "Nombre", type: "text", required: true },
    { name: "cat_activo", label: "Estado", type: "boolean" },
];

const formFieldsVer = [
    { name: "cat_nombre", label: "Categoría", type: "text" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/categorias/${itemSeleccionado.value.id}/eliminar`
            );
            await fetchCategorias();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error("No puedes eliminar esta categoría, por el momento solo desactivelo", {
                autoClose: 5000,
                position: "bottom-right",
                style: {
                    width: "400px",
                },
                className: "border-l-4 border-red-500 p-4",
            });
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

const abrirEliminarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};

const alertaEliminar = () => {
    fetchCategorias();
    toast.success("Categoria eliminado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
}

onMounted(() => fetchCategorias());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Categorías
        </h1>
        <div
            class="flex flex-col items-center justify-between mb-4 sm:flex-row"
        >
            <div class="relative w-full mb-2 sm:w-auto sm:mb-0">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                >
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input
                    type="text"
                    v-model="buscarQuery"
                    placeholder="Buscar..."
                    class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 sm:w-auto focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5"
                />
            </div>
            <ButtonNuevo @click="mostrarModalCrear = true" />
        </div>

        <Table
            :headers="headers"
            entityType="categoria"
            :items="filtrarCategorias"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            itemName="Categoría"
            endpoint="/categorias"
            @cerrar="cerrarCrearModal"
            @crear="fetchCategorias"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Categoría"
            :formFieldsVer="formFieldsVer"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Categoría"
            :formFields="formFields"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/categorias"
            @cerrar="cerrarEditarModal"
            @update="fetchCategorias"
        />

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Categoría"
            fieldName="cat_nombre"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />
    </div>
</template>
