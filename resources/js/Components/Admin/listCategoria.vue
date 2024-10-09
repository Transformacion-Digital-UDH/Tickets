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
const archivadoActivo = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(6);
const totalPages = ref(1);

const headers = ["N°", "Nombre", "Estado"];

const toggleArchivedFilter = () => {
    archivadoActivo.value = !archivadoActivo.value;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const resetArchivedFilter = () => {
    archivadoActivo.value = false;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const getArchivedFilterFromLocalStorage = () => {
    const storedValue = localStorage.getItem("archivadoActivo");
    archivadoActivo.value = storedValue === "true";
};

const filtrarCategorias = computed(() => {
    let filteredCategorias = categorias.value;

    if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredCategorias = filteredCategorias.filter((categoria) => {
            return categoria.cat_nombre.toLowerCase().includes(query);
        });
    }

    if (archivadoActivo.value) {
        return filteredCategorias.filter(
            (categoria) => categoria.cat_activo === 0
        );
    } else {
        return filteredCategorias.filter(
            (categoria) => categoria.cat_activo === 1
        );
    }
});

const mapCategoriaData = (categoria, index, totalCategorias) => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return {
        id: categoria.id,
        cat_nombre: categoria.cat_nombre,
        cat_activo: categoria.cat_activo,
        row_number: totalCategorias - (startIndex + index),
    };
};

const changePage = (pageNumber) => {
    currentPage.value = pageNumber;
    localStorage.setItem("currentPage", pageNumber);
    fetchCategorias(pageNumber);
};

const fetchCategorias = async (page = 1) => {
    try {
        const response = await axios.get(`/categoriaspaginated?page=${page}`);

        const totalCategorias = response.data.total;
        const categoriaData = response.data.data.map((categoria, index) =>
            mapCategoriaData(categoria, index, totalCategorias)
        );

        categorias.value = categoriaData;
        currentPage.value = response.data.current_page;
        itemsPerPage.value = response.data.per_page;
        totalPages.value = response.data.last_page;
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

const cerrarCrearModal = async () => {
    mostrarModalCrear.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchCategorias(currentPage.value);
};

const abrirDetallesModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = async () => {
    mostrarModalDetalles.value = false;
    localStorage.setItem("currentPage", 1);
    await fetchCategorias(currentPage.value);
};

const abrirEditarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = async () => {
    mostrarModalEditar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchCategorias(currentPage.value);
};

const abrirEliminarModal = (categoria) => {
    itemSeleccionado.value = categoria;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = async () => {
    mostrarModalEliminar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchCategorias(currentPage.value);
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

onMounted(() => fetchCategorias(), getArchivedFilterFromLocalStorage());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Categorías
        </h1>
        <div
            class="flex flex-col items-center justify-between mb-4 sm:flex-row"
        >
            <div
                class="relative w-full mb-2 sm:w-auto sm:mb-0 flex items-center"
            >
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
                <button
                    @click="toggleArchivedFilter"
                    class="ml-2 flex items-center px-2 py-1 rounded-md hover:bg-opacity-80 font-semibold"
                    :class="
                        archivadoActivo
                            ? 'bg-red-500 text-white'
                            : 'bg-[#2EBAA1] text-white'
                    "
                >
                    <i class="fas fa-filter mr-1"></i>
                    <span>{{ archivadoActivo ? "Archivado" : "Activo" }}</span>
                    <i
                        class="fas fa-times ml-2"
                        v-if="archivadoActivo"
                        @click.stop="resetArchivedFilter"
                    ></i>
                </button>
            </div>
            <ButtonNuevo @click="mostrarModalCrear = true" />
        </div>

        <Table
            :headers="headers"
            :items="filtrarCategorias"
            :currentPage="currentPage"
            :totalPages="totalPages"
            entityType="categoria"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
            @changePage="changePage"
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
