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

const soportes = ref([]);
const sedes = ref([]);
const formFields = ref([]);
const formFieldsVer = ref([]);
const buscarQuery = ref("");
const mostrarModalCrear = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);
const passwordGenerada = ref("");

const headers = ["N°", "Nombre", "Sede", "Estado"];

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
        toast.error("Error al cargar los soportes técnicos", {
            autoClose: 5000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        });
    }
};

const fetchSedes = async () => {
    try {
        const response = await axios.get("/sedes");
        sedes.value = response.data.filter((sede) => sede.sed_activo).map((sede) => ({
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
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "sed_id") {
                return {
                    ...field,
                    options: sedes.value,
                };
            }
            return field;
        });
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
    { name: "activo", label: "Estado", type: "boolean" },
];

formFieldsVer.value = [
    { name: "name", label: "Nombre", type: "text" },
    { name: "sed_nombre", label: "Sede", type: "text" },
    { name: "email", label: "Correo", type: "email" },
    { name: "celular", label: "Teléfono", type: "text" },
];

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/soportes/${itemSeleccionado.value.id}/eliminar`
            );
            await fetchSoportes();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error("No puedes eliminar a este soporte técnico, por el momento solo desactivelo", {
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

const abrirEliminarModal = (soporte) => {
    itemSeleccionado.value = soporte;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};

const alertaEliminar = () => {
    fetchSoportes();
    toast.success("Soporte técnico eliminado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
}

onMounted(() => {
    fetchSoportes();
    fetchSedes();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Soportes Técnicos
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
            :items="filtrarSoportes"
            entityType="user"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @eliminar="abrirEliminarModal"
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
            :formFieldsVer="formFieldsVer"
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

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Soporte"
            fieldName="name"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />
    </div>
</template>
