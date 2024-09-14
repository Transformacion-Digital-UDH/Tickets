<script setup>
import { ref, onMounted, computed } from "vue";
import Table from "../Table.vue";
import ModalCrear from "../ModalCrear.vue";
import ModalVer from "../ModalVer.vue";
import ModalEditar from "../ModalEditar.vue";
import ModalDesactivar from "../ModalDesactivar.vue";
import ModalActivar from "../ModalActivar.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import axios from "axios";

library.add(faPlus);

const pabellons = ref([]);
const sedes = ref([]);
const formFields = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalDesactivar = ref(false);
const mostrarModalActivar = ref(false);
const itemSeleccionado = ref(null);

const headers = ["N°", "Pabellones", "Sede", "Estado"];

const filtrarPabellones = computed(() => {
    return pabellons.value.filter(
        (pabellon) =>
            pabellon.pab_nombre
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchPabellones = async () => {
    try {
        const response = await axios.get("/pabellons");
        pabellons.value = response.data.map((pabellon) => ({
            id: pabellon.id,
            pab_nombre: pabellon.pab_nombre,
            sed_id: pabellon.sed_id,
            sed_nombre: pabellon.sede.sed_nombre,
            pab_activo: pabellon.pab_activo,
        }));
    } catch (error) {
        console.error("Error al cargar los pabellones:", error);
    }
};

const fetchSedes = async () => {
    try {
        const response = await axios.get("/sedes");
        sedes.value = response.data.map((sede) => ({
            value: sede.id,
            text: sede.sed_nombre,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "sed_id") {
                return {
                    ...field,
                    options: sedes.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar las sedes:", error);
    }
};

formFields.value = [
    { name: "pab_nombre", label: "Pabellon", type: "text" },
    {
        name: "sed_id",
        label: "Sede",
        type: "select",
        options: sedes.value,
    },
];

const desactivarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/pabellons/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchPabellones();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar el pabellon:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/pabellons/${itemSeleccionado.value.id}/activar`);
            await fetchPabellones();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al eliminar el pabellon:", error);
        }
    }
};

const abrirCrearModal = () => {
    mostrarModalCrear.value = true;
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (pabellon) => {
    itemSeleccionado.value = pabellon;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => {
    fetchPabellones();
    fetchSedes();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">
            Lista de Pabellones
        </h1>

        <div class="flex items-center justify-between mb-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input type="text" v-model="buscarQuery" placeholder="Buscar..."
                    class="flex-grow px-12 pl-10 placeholder-gray-400 border border-gray-300 rounded-md focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5" />
            </div>

            <button @click="abrirCrearModal"
                class="flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gradient-to-r from-green-200 to-[#2EBAA1] rounded-lg shadow-md hover:from-green-400 hover:to-[#2EBAA1]">
                <font-awesome-icon icon="plus" class="mr-2 text-lg" />
                Nuevo
            </button>
        </div>

        <Table :headers="headers" :items="filtrarPabellones" @view="abrirDetallesModal" @edit="abrirEditarModal"
            @activar="abrirActivarModal" @desactivar="abrirDesactivarModal" />

        <ModalCrear v-if="mostrarModalCrear" :formFields="formFields" :sedes="sedes" itemName="Pabellon"
            endpoint="/pabellons" @cerrar="cerrarCrearModal" @crear="fetchPabellones" />

        <ModalVer v-if="mostrarModalDetalles" :item="itemSeleccionado" itemName="Pabellon" :formFields="formFields"
            :mostrarModalDetalles="mostrarModalDetalles" @close="cerrarDetallesModal" />

        <ModalEditar v-if="mostrarModalEditar" :item="itemSeleccionado" itemName="Pabellon" :formFields="formFields"
            :sedes="sedes" :mostrarModalEditar="mostrarModalEditar" endpoint="/pabellons" @cerrar="cerrarEditarModal"
            @update="fetchPabellones" />

        <ModalDesactivar v-if="mostrarModalDesactivar" :item="itemSeleccionado" itemName="Pabellon" fieldName="name"
            @cancelar="cerrarDesactivarModal" @confirmar="desactivarItem" />

        <ModalActivar v-if="mostrarModalActivar" :item="itemSeleccionado" itemName="Pabellon" fieldName="name"
            @cancelar="cerrarActivarModal" @confirmar="activarItem" />
    </div>
</template>
