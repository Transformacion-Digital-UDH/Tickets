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

const aulas = ref([]);
const pabellons = ref([]);
const formFields = ref([]);
const formFieldsVer = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);

const headers = ["N°", "Aula", "Pabellon", "Estado"];

const filtrarAulas = computed(() => {
    return aulas.value.filter((aula) =>
        aula.aul_numero.toLowerCase().includes(buscarQuery.value.toLowerCase())
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
        toast.error("Error al cargar las aulas", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

const fetchPabellones = async () => {
    try {
        const response = await axios.get("/pabellones");
        pabellons.value = response.data
            .filter((pabellon) => pabellon.pab_activo)
            .map((pabellon) => ({
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
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "pab_id") {
                return {
                    ...field,
                    options: pabellons.value,
                };
            }
            return field;
        });
    } catch (error) {
        toast.error("Error al cargar los pabellones", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

formFields.value = [
    { name: "aul_numero", label: "Aula", type: "text", required: true },
    {
        name: "pab_id",
        label: "Pabellon",
        type: "select",
        required: true,
        options: pabellons.value,
    },
    { name: "aul_activo", label: "Estado", type: "boolean" },
];

formFieldsVer.value = [
    { name: "aul_numero", label: "Aula", type: "text" },
    { name: "pab_nombre", label: "Pabellon", type: "text" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(`/aulas/${itemSeleccionado.value.id}/eliminar`);
            await fetchAulas();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error(
                "No puedes eliminar esta aula, por el momento solo desactivelo",
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

const abrirEliminarModal = (aula) => {
    itemSeleccionado.value = aula;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};

const alertaEliminar = () => {
    fetchAulas();
    toast.success("Aula eliminado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

onMounted(() => {
    fetchAulas();
    fetchPabellones();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Aulas
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
            entityType="aula"
            :items="filtrarAulas"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :pabellons="pabellons"
            itemName="Aula"
            endpoint="/aulas"
            @cerrar="cerrarCrearModal"
            @crear="fetchAulas"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Aula"
            :formFieldsVer="formFieldsVer"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Aula"
            :formFields="formFields"
            :pabellons="pabellons"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/aulas"
            @cerrar="cerrarEditarModal"
            @update="fetchAulas"
        />

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Aula"
            fieldName="name"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />
    </div>
</template>
