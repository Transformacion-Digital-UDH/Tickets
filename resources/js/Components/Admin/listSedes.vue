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

const sedes = ref([]);
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

const headers = ["N°", "Nombre", "Ciudad", "Estado"];

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

const validatePhoneNumber = (telefono) => {
    const phone = telefono.toString();
    return phone.startsWith("+51") ? phone : `+51 ${phone}`;
};

const filtrarSedes = computed(() => {
    let filteredSedes = sedes.value;

    if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredSedes = filteredSedes.filter((sede) => {
            return (
                sede.sed_nombre.toLowerCase().includes(query) ||
                sede.sed_ciudad.toLowerCase().includes(query) ||
                sede.sed_direccion.toLowerCase().includes(query) ||
                sede.sed_telefono.toLowerCase().includes(query)
            );
        });
    }

    if (archivadoActivo.value) {
        return filteredSedes.filter((sede) => sede.sed_activo === 0);
    } else {
        return filteredSedes.filter((sede) => sede.sed_activo === 1);
    }
});

const mapSedeData = (sede, index, totalSedes) => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return {
        id: sede.id,
        sed_nombre: sede.sed_nombre,
        sed_direccion: sede.sed_direccion,
        sed_ciudad: sede.sed_ciudad,
        sed_telefono: validatePhoneNumber(sede.sed_telefono),
        sed_activo: sede.sed_activo,
        row_number: totalSedes - (startIndex + index),
    };
};

const changePage = (pageNumber) => {
    currentPage.value = pageNumber;
    fetchSedes(pageNumber);
};

const fetchSedes = async (page = 1) => {
    try {
        const response = await axios.get(`/sedespaginated?page=${page}`);

        const totalSedes = response.data.total;
        const sedeData = response.data.data.map((sede, index) =>
            mapSedeData(sede, index, totalSedes)
        );

        sedes.value = sedeData;
        currentPage.value = response.data.current_page;
        itemsPerPage.value = response.data.per_page;
        totalPages.value = response.data.last_page;
    } catch (error) {
        toast.error("Error al cargar las sedes", {
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
    { name: "sed_nombre", label: "Nombre", type: "text", required: true },
    { name: "sed_direccion", label: "Dirección", type: "text", required: true },
    { name: "sed_ciudad", label: "Ciudad", type: "text", required: true },
    { name: "sed_telefono", label: "Teléfono", type: "number", required: true },
    { name: "sed_activo", label: "Estado", type: "boolean" },
];

const formFieldsVer = [
    { name: "sed_nombre", label: "Sede", type: "text" },
    { name: "sed_direccion", label: "Dirección", type: "text" },
    { name: "sed_ciudad", label: "Ciudad", type: "text" },
    { name: "sed_telefono", label: "Teléfono", type: "number" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(`/sedes/${itemSeleccionado.value.id}/eliminar`);
            await fetchSedes();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error(
                "No puedes eliminar esta sede, por el momento solo desactivelo",
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
    localStorage.setItem("currentPage", 1);
    await fetchSedes(currentPage.value);
};

const abrirDetallesModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = async () => {
    mostrarModalDetalles.value = false;
    localStorage.setItem("currentPage", 1);
    await fetchSedes(currentPage.value);
};

const abrirEditarModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = async () => {
    mostrarModalEditar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchSedes(currentPage.value);
};

const abrirEliminarModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = async () => {
    mostrarModalEliminar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchSedes(currentPage.value);
};

const alertaEliminar = () => {
    fetchSedes();
    toast.success("Sede eliminada correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

onMounted(() => fetchSedes(), getArchivedFilterFromLocalStorage());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Sedes
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
            :items="filtrarSedes"
            :currentPage="currentPage"
            :totalPages="totalPages"
            entityType="sede"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
            @changePage="changePage"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            itemName="Sede"
            endpoint="/sedes"
            @cerrar="cerrarCrearModal"
            @crear="fetchSedes"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Sede"
            :formFieldsVer="formFieldsVer"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Sede"
            :formFields="formFields"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/sedes"
            @cerrar="cerrarEditarModal"
            @update="fetchSedes"
        />

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Sede"
            fieldName="sed_nombre"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />
    </div>
</template>
