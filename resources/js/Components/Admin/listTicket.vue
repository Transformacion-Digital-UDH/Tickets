<script setup>
import { ref, onMounted, computed } from "vue";
import CardTickets from "@/Components/CardTickets.vue";
import ModalCrear from "@/Components/ModalCrear.vue";
import ModalAsignar from "@/Components/ModalAsignar.vue";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";
import ButtonNuevo from "@/Components/ButtonNuevo.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faThLarge, faTable } from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

library.add(faThLarge, faTable);

const tickets = ref([]);
const prioridades = ref([]);
const soportes = ref([]);
const usuarios = ref([]);
const categorias = ref([]);
const pabellones = ref([]);
const aulas = ref([]);
const formFields = ref([]);
const formFieldsAsignar = ref([]);
const formFieldsVer = ref([]);
const buscarQuery = ref("");
const isCardView = ref(true);
const mostrarModalCrear = ref(false);
const mostrarModalAsignar = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);

const headers = [
    "Título",
    "Descripción",
    "Prioridad",
    "Estado",
    "Usuario",
    "Categoría",
];

const filtrarTickets = computed(() => {
    return tickets.value.filter(
        (ticket) =>
            ticket.tic_titulo
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            ticket.tic_descripcion
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            ticket.tic_estado
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            ticket.pri_nombre
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            ticket.name
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase()) ||
            ticket.cat_nombre
                .toLowerCase()
                .includes(buscarQuery.value.toLowerCase())
    );
});

const fetchTickets = async () => {
    try {
        const response = await axios.get("/tickets");
        tickets.value = response.data.map((ticket) => ({
            id: ticket.id,
            tic_titulo: ticket.tic_titulo,
            tic_descripcion: ticket.tic_descripcion,
            pri_id: ticket.pri_id,
            pri_nombre: ticket.prioridad ? ticket.prioridad.pri_nombre : "",
            use_id: ticket.use_id,
            name: ticket.user ? ticket.user.name : "",
            cat_id: ticket.cat_id,
            cat_nombre: ticket.categoria ? ticket.categoria.cat_nombre : "",
            pab_id: ticket.pab_id,
            pab_nombre: ticket.pabellon ? ticket.pabellon.pab_nombre : "",
            aul_id: ticket.aul_id,
            aul_numero: ticket.aula ? ticket.aula.aul_numero : "",
            tic_estado: ticket.tic_estado,
            tic_activo: ticket.tic_activo,
        }));
    } catch (error) {
        console.error("Error al cargar los tickets:", error);
    }
};

const fetchPrioridades = async () => {
    try {
        const response = await axios.get("/prioridades");
        prioridades.value = response.data.filter((prioridad) => prioridad.pri_activo).map((prioridad) => ({
            value: prioridad.id,
            text: prioridad.pri_nombre,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "pri_id") {
                return {
                    ...field,
                    options: prioridades.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "pri_id") {
                return {
                    ...field,
                    options: prioridades.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar las prioridades:", error);
    }
};

const fetchSoportes = async () => {
    try {
        const response = await axios.get("/soportes");
        soportes.value = response.data.filter((soporte) => soporte.activo).map((soporte) => ({
            value: soporte.id,
            text: soporte.name,
        }));
        formFieldsAsignar.value = formFieldsAsignar.value.map((field) => {
            if (field.name == "use_id") {
                return {
                    ...field,
                    options: soportes.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar los soportes técnicos:", error);
    }
};

const fetchUsuarios = async () => {
    try {
        const response = await axios.get("/usuarios");
        usuarios.value = response.data.filter((usuario) => usuario.activo).map((usuario) => ({
            value: usuario.id,
            text: usuario.name,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "use_id") {
                return {
                    ...field,
                    options: usuarios.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "use_id") {
                return {
                    ...field,
                    options: usuarios.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar los usuarios:", error);
    }
};

const fetchCategorias = async () => {
    try {
        const response = await axios.get("/categorias");
        categorias.value = response.data.filter((categoria) => categoria.cat_activo).map((categoria) => ({
            value: categoria.id,
            text: categoria.cat_nombre,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "cat_id") {
                return {
                    ...field,
                    options: categorias.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "cat_id") {
                return {
                    ...field,
                    options: categorias.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar las categorías:", error);
    }
};

const fetchPabellones = async () => {
    try {
        const response = await axios.get("/pabellones");
        pabellones.value = response.data.filter((pabellon) => pabellon.pab_activo).map((pabellon) => ({
            value: pabellon.id,
            text: pabellon.pab_nombre,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "pab_id") {
                return {
                    ...field,
                    options: pabellones.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "pab_id") {
                return {
                    ...field,
                    options: pabellones.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar los pabellones:", error);
    }
};

const fetchAulas = async () => {
    try {
        const response = await axios.get("/aulas");
        aulas.value = response.data.filter((aula) => aula.aul_activo).map((aula) => ({
            value: aula.id,
            text: aula.aul_numero,
        }));
        formFields.value = formFields.value.map((field) => {
            if (field.name === "aul_id") {
                return {
                    ...field,
                    options: aulas.value,
                };
            }
            return field;
        });
        formFieldsVer.value = formFieldsVer.value.map((field) => {
            if (field.name === "aul_id") {
                return {
                    ...field,
                    options: aulas.value,
                };
            }
            return field;
        });
    } catch (error) {
        console.error("Error al cargar las aulas:", error);
    }
};

formFields.value = [
    { name: "tic_titulo", label: "Título", type: "text" },
    {
        name: "pri_id",
        label: "Prioridad",
        type: "select",
        options: prioridades.value,
    },
    {
        name: "use_id",
        label: "Usuario",
        type: "select",
        options: usuarios.value,
    },
    {
        name: "cat_id",
        label: "Categoría",
        type: "select",
        options: categorias.value,
    },
    {
        name: "pab_id",
        label: "Pabellón",
        type: "select",
        options: pabellones.value,
    },
    { name: "aul_id", label: "Aula", type: "select", options: aulas.value },
    { name: "tic_descripcion", label: "Descripción", type: "textarea" },
    { name: "tic_activo", label: "Activo", type: "boolean" },
];

formFieldsAsignar.value = [
    {
        name: "use_id",
        label: "Soporte",
        type: "select",
        options: soportes.value,
    },
];

formFieldsVer.value = [
    { name: "tic_titulo", label: "Título", type: "text" },
    { name: "pri_nombre", label: "Prioridad", type: "text" },
    { name: "name", label: "Usuario", type: "text" },
    { name: "cat_nombre", label: "Categoría", type: "text" },
    { name: "pab_nombre", label: "Pabellón", type: "text" },
    { name: "aul_numero", label: "Aula", type: "text" },
    { name: "tic_descripcion", label: "Descripción", type: "textarea" },
    { name: "tic_estado", label: "Estado", type: "text" },
];

const asignarSoporte = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.put(
                `/tickets/${itemSeleccionado.value.id}`
            );
            await fetchTickets();
            mostrarModalAsignar.value = false;
        } catch (error) {
            console.error("Error al asignar un soporte técnico:", error);
        }
    }
}

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/tickets/${itemSeleccionado.value.id}/eliminar`
            );
            await fetchTickets();
            mostrarModalEliminar.value = false;
        } catch (error) {
            console.error("Error al eliminar al ticket:", error);
        }
    }
};

const cerrarCrearModal = () => {
    mostrarModalCrear.value = false;
};

const abrirAsignarModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalAsignar.value = true;
};

const cerrarAsignarModal = () => {
    mostrarModalAsignar.value = false;
};

const abrirDetallesModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const abrirEditarModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const abrirEliminarModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};

onMounted(() => {
    fetchTickets();
    fetchPrioridades();
    fetchSoportes();
    fetchUsuarios();
    fetchCategorias();
    fetchPabellones();
    fetchAulas();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Tickets
        </h1>
        <div class="flex items-center justify-between mb-4">
            <div class="relative w-full sm:w-auto">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input type="text" v-model="buscarQuery" placeholder="Buscar..."
                    class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 sm:w-auto focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5" />
            </div>
            <div class="flex items-center space-x-1">
                <font-awesome-icon @click="isCardView = true" :class="{ 'bg-gray-200': isCardView }"
                    class="flex items-center px-4 py-2 text-sm font-semibold bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100"
                    icon="th-large" />
                <font-awesome-icon @click="isCardView = false" :class="{ 'bg-gray-200': !isCardView }"
                    class="flex items-center px-4 py-2 text-sm font-semibold bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-100"
                    icon="table" />
                <ButtonNuevo @click="mostrarModalCrear = true" />
            </div>
        </div>

        <CardTickets :isCardView="isCardView" :headers="headers" :tickets="filtrarTickets" @asign="abrirAsignarModal"
            @view="abrirDetallesModal" @edit="abrirEditarModal" @eliminar="abrirEliminarModal" />

        <ModalCrear v-if="mostrarModalCrear" :formFields="formFields" :prioridads="prioridades" :usuarios="usuarios"
            :categorias="categorias" :pabellons="pabellones" :aulas="aulas" itemName="Ticket" endpoint="/tickets"
            @cerrar="cerrarCrearModal" @crear="fetchTickets" />

        <ModalAsignar v-if="mostrarModalAsignar" :formFieldsAsignar="formFieldsAsignar" :soportes="soportes"
            itemName="Soporte Técnico" endpoint="/tickets" @cerrar="cerrarAsignarModal" @crear="asignarSoporte" />

        <ModalVer v-if="mostrarModalDetalles" :item="itemSeleccionado" itemName="Ticket" :formFieldsVer="formFieldsVer"
            :mostrarModalDetalles="mostrarModalDetalles" @close="cerrarDetallesModal" />

        <ModalEditar v-if="mostrarModalEditar" :item="itemSeleccionado" itemName="Ticket" :formFields="formFields"
            :prioridads="prioridades" :usuarios="usuarios" :categorias="categorias" :pabellons="pabellones"
            :aulas="aulas" :mostrarModalEditar="mostrarModalEditar" endpoint="/tickets" @cerrar="cerrarEditarModal"
            @update="fetchTickets" />

        <ModalEliminar v-if="mostrarModalEliminar" :item="itemSeleccionado" itemName="Ticket" fieldName="tic_titulo"
            @cancelar="cerrarEliminarModal" @confirmar="eliminarItem" />
    </div>
</template>
