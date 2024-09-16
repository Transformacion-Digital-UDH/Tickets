<script setup>
import { ref, onMounted, computed } from "vue";
import Table from "../Table.vue";
import ModalCrear from "../ModalCrear.vue";
import ModalVer from "../ModalVer.vue";
import ModalEditar from "../ModalEditar.vue";
import ModalDesactivar from "../ModalDesactivar.vue";
import ModalActivar from "../ModalActivar.vue";
import ButtonNuevo from "../ButtonNuevo.vue";
import axios from "axios";

const usuarios = ref([]);
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

const filtrarUsuarios = computed(() => {
    return usuarios.value.filter(
        (usuario) =>
            usuario.name
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            usuario.email
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            usuario.celular
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchUsuarios = async () => {
    try {
        const response = await axios.get("/usuarios");
        usuarios.value = response.data.map((usuario) => ({
            id: usuario.id,
            name: usuario.name,
            email: usuario.email,
            celular: validatePhoneNumber(usuario.celular),
            sed_id: usuario.sed_id,
            sed_nombre: usuario.sede ? usuario.sede.sed_nombre : "",
            activo: usuario.activo,
        }));
    } catch (error) {
        console.error("Error al cargar los usuarios:", error);
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
                `/usuarios/${itemSeleccionado.value.id}/desactivar`
            );
            await fetchUsuarios();
            mostrarModalDesactivar.value = false;
        } catch (error) {
            console.error("Error al desactivar al usuario:", error);
        }
    }
};

const activarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(`/usuarios/${itemSeleccionado.value.id}/activar`);
            await fetchUsuarios();
            mostrarModalActivar.value = false;
        } catch (error) {
            console.error("Error al eliminar al usuario:", error);
        }
    }
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirDetallesModal = (usuario) => {
    itemSeleccionado.value = usuario;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (usuario) => {
    itemSeleccionado.value = usuario;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirDesactivarModal = (usuario) => {
    itemSeleccionado.value = usuario;
    mostrarModalDesactivar.value = true;
};

const cerrarDesactivarModal = () => {
    mostrarModalDesactivar.value = false;
};

const abrirActivarModal = (usuario) => {
    itemSeleccionado.value = usuario;
    mostrarModalActivar.value = true;
};

const cerrarActivarModal = () => {
    mostrarModalActivar.value = false;
};

onMounted(() => {
    fetchUsuarios();
    fetchSedes();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-[20px] font-bold text-gray-500">
            Lista de Usuarios
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
            <ButtonNuevo @click="mostrarModalCrear = true" />
        </div>

        <Table
            :headers="headers"
            :items="filtrarUsuarios"
            @view="abrirDetallesModal"
            @edit="abrirEditarModal"
            @activar="abrirActivarModal"
            @desactivar="abrirDesactivarModal"
        />

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :sedes="sedes"
            itemName="Usuario"
            endpoint="/usuarios"
            @cerrar="cerrarCrearModal"
            @crear="fetchUsuarios"
        />

        <ModalVer
            v-if="mostrarModalDetalles"
            :item="itemSeleccionado"
            itemName="Usuario"
            :formFields="formFields"
            :mostrarModalDetalles="mostrarModalDetalles"
            @close="cerrarDetallesModal"
        />

        <ModalEditar
            v-if="mostrarModalEditar"
            :item="itemSeleccionado"
            itemName="Usuario"
            :formFields="formFields"
            :sedes="sedes"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/usuarios"
            @cerrar="cerrarEditarModal"
            @update="fetchUsuarios"
        />

        <ModalDesactivar
            v-if="mostrarModalDesactivar"
            :item="itemSeleccionado"
            itemName="Usuario"
            fieldName="name"
            @cancelar="cerrarDesactivarModal"
            @confirmar="desactivarItem"
        />

        <ModalActivar
            v-if="mostrarModalActivar"
            :item="itemSeleccionado"
            itemName="Usuario"
            fieldName="name"
            @cancelar="cerrarActivarModal"
            @confirmar="activarItem"
        />
    </div>
</template>
