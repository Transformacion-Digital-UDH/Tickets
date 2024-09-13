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

const soportes = ref([]);
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
    const phone = telefono.toString();
    return phone.startsWith("+51") ? phone : `+51 ${phone}`;
};

const filtrarSoportes = computed(() => {
    return soportes.value.filter(
        (soporte) =>
            soporte.name
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            soporte.email
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            soporte.celular
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchSoportes = async () => {
    try {
        const response = await axios.get("/soportes");
        soportes.value = response.data.map((soporte) => ({
            id: soporte.id,
            name: soporte.name,
            email: soporte.email,
            celular: validatePhoneNumber(soporte.celular),
            sed_id: soporte.sed_id,
            sed_nombre: soporte.sede.sed_nombre,
            activo: soporte.activo,
        }));
    } catch (error) {
        console.error("Error al cargar los soportes técnicos:", error);
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
                `/soportes/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchSoportes();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar la sede:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/soportes/${itemSeleccionado.value.id}/activar`);
            await fetchSoportes();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al eliminar al soporte técnico:", error);
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

const abrirDetallesModal = (soporte) => {
    itemSeleccionado.value = soporte;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (soporte) => {
    if (sedes.value.length === 0) {
        fetchSedes();
    }
    itemSeleccionado.value = soporte;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (soporte) => {
    itemSeleccionado.value = soporte;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (soporte) => {
    itemSeleccionado.value = soporte;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => {
    fetchSoportes();
    fetchSedes();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">
            Lista de Soportes Técnicos
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
            :items="filtrarSoportes"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @activar="abrirActivarModal"
            @desactivar="abrirDesactivarModal"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :sedes="sedes"
            itemName="Soporte"
            endpoint="/soportes"
            @cerrar="cerrarCrearModal"
            @crear="fetchSoportes"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Soporte"
            :formFields="formFields"
            :sedes="sedes"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Soporte"
            :formFields="formFields"
            :sedes="sedes"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/soportes"
            @cerrar="cerrarEditarModal"
            @update="fetchSoportes"
        />

        <ModalDesactivar
            v-if="mostrarModalDesactivar"
            :item="itemSeleccionado"
            itemName="Soporte"
            fieldName="name"
            @cancelar="cerrarDesactivarModal"
            @confirmar="desactivarItem"
        />

        <ModalActivar
            v-if="mostrarModalActivar"
            :item="itemSeleccionado"
            itemName="Soporte"
            fieldName="name"
            @cancelar="cerrarActivarModal"
            @confirmar="activarItem"
        />
    </div>
</template>
