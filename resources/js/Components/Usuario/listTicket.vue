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
    { name: "cat_nombre", label: "Nombre", type: "text" },
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
            toast.error(
                "No puedes eliminar esta categoría, por el momento solo desactivelo",
                {
                    autoClose: 5000,
                    position: "bottom-right",
                    style: {
                        width: "400px",
                    },
                    className: "border-l-4 border-red-500 p-4",
                }
            );
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

const alertaCreacion = () => {
    fetchCategorias();
    toast.success("Categoria creado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

const alertaEditar = () => {
    fetchCategorias();
    toast.success("Categoria actualizado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
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
};

onMounted(() => fetchCategorias());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Mis Tickets
        </h1>
        <div class="overflow-x-auto bg-[#2EBAA1] rounded-lg shadow-md p-6">
            <div
                class="overflow-x-auto bg-white rounded-lg shadow-md p-4 flex mb-3 justify-center"
            >
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-orange-600 rounded-lg shadow-md hover:bg-orange-800"
                >
                    Abierto
                </button>
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-blue-600 rounded-lg shadow-md hover:bg-blue-800"
                >
                    En progreso
                </button>
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-red-600 rounded-lg shadow-md hover:bg-red-800"
                >
                    Cerrado
                </button>
            </div>
            <Table
                :headers="headers"
                entityType="categoria"
                :items="filtrarCategorias"
                @view="abrirDetallesModal"
                @edit="abrirEditarModal"
                @eliminar="abrirEliminarModal"
            />
        </div>
    </div>
</template>
