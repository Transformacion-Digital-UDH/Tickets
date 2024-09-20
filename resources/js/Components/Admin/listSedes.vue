<script setup>
import { ref, onMounted, computed } from "vue";
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

const headers = ["N°", "Nombre", "Ciudad", "Estado"];

const validatePhoneNumber = (telefono) => {
    const phone = telefono.toString();
    return phone.startsWith("+51") ? phone : `+51 ${phone}`;
};

const filtrarSedes = computed(() => {
    return sedes.value.filter(
        (sede) =>
            sede.sed_nombre
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            sede.sed_ciudad
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            sede.sed_direccion
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            sede.sed_telefono
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchSedes = async () => {
    try {
        const response = await axios.get("/sedes");
        sedes.value = response.data.map((sede) => ({
            id: sede.id,
            sed_nombre: sede.sed_nombre,
            sed_direccion: sede.sed_direccion,
            sed_ciudad: sede.sed_ciudad,
            sed_telefono: validatePhoneNumber(sede.sed_telefono),
            sed_activo: sede.sed_activo,
        }));
    } catch (error) {
        console.error("Error al cargar las sedes:", error);
    }
};

const formFields = [
    { name: "sed_nombre", label: "Nombre", type: "text" },
    { name: "sed_direccion", label: "Dirección", type: "text" },
    { name: "sed_ciudad", label: "Ciudad", type: "text" },
    { name: "sed_telefono", label: "Teléfono", type: "text" },
    { name: "sed_activo", label: "Estado", type: "boolean" },
];

const formFieldsVer = [
    { name: "sed_nombre", label: "Sede", type: "text" },
    { name: "sed_direccion", label: "Dirección", type: "text" },
    { name: "sed_ciudad", label: "Ciudad", type: "text" },
    { name: "sed_telefono", label: "Teléfono", type: "text" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(`/sedes/${itemSeleccionado.value.id}/eliminar`);
            await fetchSedes();
            mostrarModalEliminar.value = false;
        } catch (error) {
            console.error("Error al eliminar la sede", error);
        }
    }
};

const abrirCrearModal = () => {
    mostrarModalCrear.value = true;
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirEliminarModal = (sede) => {
    itemSeleccionado.value = sede;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};

onMounted(() => fetchSedes());
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Sedes
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
            :items="filtrarSedes"
            entityType="sede"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
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
