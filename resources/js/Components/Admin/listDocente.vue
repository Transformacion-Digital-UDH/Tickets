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

const docentes = ref([]);
const sedes = ref([]);
const formFields = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalDesactivar = ref(false);
const mostrarModalActivar = ref(false);
const itemSeleccionado = ref(null);
const passwordGenerada = ref("");

const headers = ["N°", "Nombres", "Correo", "Teléfono", "Sede", "Estado"];

const generarPassword = () => {
    const caracteres =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    let password = "";
    for (let i = 0; i < 10; i++) {
        password += caracteres.charAt(
            Math.floor(Math.random() * caracteres.length)
        );
    }
    return password;
};

passwordGenerada.value = generarPassword();

const validatePhoneNumber = (telefono) => {
    if (telefono === null) {
        return "";
    }
    const phone = telefono.toString();
    return phone.startsWith("+51") ? phone : `+51 ${phone}`;
};

const filtrarDocentes = computed(() => {
    return docentes.value.filter(
        (docente) =>
            docente.name
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            docente.email
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            docente.celular
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchDocentes = async () => {
    try {
        const response = await axios.get("/docentes");
        docentes.value = response.data.map((docente) => ({
            id: docente.id,
            name: docente.name,
            email: docente.email,
            celular: validatePhoneNumber(docente.celular),
            sed_id: docente.sed_id,
            sed_nombre: docente.sede ? docente.sede.sed_nombre : "",
            activo: docente.activo,
        }));
    } catch (error) {
        console.error("Error al cargar los docentes:", error);
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
    { name: "name", label: "Nombre", type: "text" },
    {
        name: "sed_id",
        label: "Sede",
        type: "select",
        options: sedes.value,
    },
    { name: "email", label: "Correo", type: "email" },
    { name: "celular", label: "Teléfono", type: "text" },
    {
        name: "password",
        label: "Contraseña",
        type: "text",
        default: computed(() => passwordGenerada.value),
    },
];

const desactivarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/docentes/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchDocentes();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar al docente:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/docentes/${itemSeleccionado.value.id}/activar`);
            await fetchDocentes();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al eliminar al docente:", error);
        }
    }
};

const abrirCrearModal = () => {
    passwordGenerada.value = generarPassword();
    mostrarModalCrear.value = true;
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (docente) => {
    itemSeleccionado.value = docente;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (docente) => {
    itemSeleccionado.value = docente;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (docente) => {
    itemSeleccionado.value = docente;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (docente) => {
    itemSeleccionado.value = docente;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => {
    fetchDocentes();
    fetchSedes();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">
            Lista de Docentes
        </h1>

        <div class="flex items-center justify-between mb-4">
            <div class="relative">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                >
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input
                    type="text"
                    v-model="buscarQuery"
                    placeholder="Buscar..."
                    class="flex-grow px-12 pl-10 placeholder-gray-400 border border-gray-300 rounded-md focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5"
                />
            </div>

            <button
                @click="abrirCrearModal"
                class="flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gradient-to-r from-green-200 to-[#2EBAA1] rounded-lg shadow-md hover:from-green-400 hover:to-[#2EBAA1]"
            >
                <font-awesome-icon icon="plus" class="mr-2 text-lg" />
                Nuevo
            </button>
        </div>

        <Table
            :headers="headers"
            :items="filtrarDocentes"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @activar="abrirActivarModal"
            @desactivar="abrirDesactivarModal"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :sedes="sedes"
            itemName="Docente"
            endpoint="/docentes"
            @cerrar="cerrarCrearModal"
            @crear="fetchDocentes"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Docente"
            :formFields="formFields"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Docente"
            :formFields="formFields"
            :sedes="sedes"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/docentes"
            @cerrar="cerrarEditarModal"
            @update="fetchDocentes"
        />

        <ModalDesactivar
            v-if="mostrarModalDesactivar"
            :item="itemSeleccionado"
            itemName="Docente"
            fieldName="name"
            @cancelar="cerrarDesactivarModal"
            @confirmar="desactivarItem"
        />

        <ModalActivar
            v-if="mostrarModalActivar"
            :item="itemSeleccionado"
            itemName="Docente"
            fieldName="name"
            @cancelar="cerrarActivarModal"
            @confirmar="activarItem"
        />
    </div>
</template>
