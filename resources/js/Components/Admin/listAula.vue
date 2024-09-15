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

const aulas = ref([]);
const pabellons = ref([]);
const formFields = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalDesactivar = ref(false);
const mostrarModalActivar = ref(false);
const itemSeleccionado = ref(null);

const headers = ["N°", "Aula", "Pabellon", "Estado"];

const filtrarAulas = computed(() => {
    return aulas.value.filter(
        (aula) =>
            aula.aul_numero
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchAulas = async () => {
    try {
        const response = await axios.get("/aulas");
        aulas.value = response.data.map((aula) => ({
            id: aula.id,
            aul_numero: aula.aul_numero,
            pab_id: aula.pab_id,
            pab_nombre: aula.pabellon.pab_nombre,
            aul_activo: aula.aul_activo,
        }));
    } catch (error) {
        console.error("Error al cargar las aulas:", error);
    }
};

const fetchPabellones = async () => {
    try {
        const response = await axios.get("/pabellons");
        pabellons.value = response.data.map((pabellon) => ({
            value: pabellon.id,
            text: pabellon.pab_nombre,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "pab_id") {
                return {
                    ...field,
                    options: pabellons.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar los pabellones:", error);
    }
};

formFields.value = [
    { name: "aul_numero", label: "Aula", type: "text" },
    {
        name: "pab_id",
        label: "Pabellon",
        type: "select",
        options: pabellons.value,
    },
];

const desactivarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/aulas/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchAulas();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar el aula:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/aulas/${itemSeleccionado.value.id}/activar`);
            await fetchAulas();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al eliminar el aula:", error);
        }
    }
};

const abrirCrearModal = () => {
    mostrarModalCrear.value = true;
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (aula) => {
    itemSeleccionado.value = aula;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (aula) => {
    itemSeleccionado.value = aula;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (aula) => {
    itemSeleccionado.value = aula;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (aula) => {
    itemSeleccionado.value = aula;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => {
    fetchAulas();
    fetchPabellones();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">
            Lista de Aulas
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

        <Table :headers="headers" :items="filtrarAulas" @view="abrirDetallesModal" @edit="abrirEditarModal"
            @activar="abrirActivarModal" @desactivar="abrirDesactivarModal" />

        <ModalCrear v-if="mostrarModalCrear" :formFields="formFields" :pabellons="pabellons" itemName="Aula"
            endpoint="/aulas" @cerrar="cerrarCrearModal" @crear="fetchAulas" />

        <ModalVer v-if="mostrarModalDetalles" :item="itemSeleccionado" itemName="Aula" :formFields="formFields"
            :mostrarModalDetalles="mostrarModalDetalles" @close="cerrarDetallesModal" />

        <ModalEditar v-if="mostrarModalEditar" :item="itemSeleccionado" itemName="Aula" :formFields="formFields"
            :pabellons="pabellons" :mostrarModalEditar="mostrarModalEditar" endpoint="/aulas" @cerrar="cerrarEditarModal"
            @update="fetchAulas" />

        <ModalDesactivar v-if="mostrarModalDesactivar" :item="itemSeleccionado" itemName="Aula" fieldName="name"
            @cancelar="cerrarDesactivarModal" @confirmar="desactivarItem" />

        <ModalActivar v-if="mostrarModalActivar" :item="itemSeleccionado" itemName="Aula" fieldName="name"
            @cancelar="cerrarActivarModal" @confirmar="activarItem" />
    </div>
</template>
