<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";

const activeTab = ref("open");
const prioridades = ref([]);
const categorias = ref([]);
const pabellones = ref([]);
const aulas = ref([]);
const formFields = ref([]);
const formFieldsVer = ref([]);
const tickets = ref({
    open: [],
    inProgress: [],
    closed: [],
});
const itemSeleccionado = ref(null);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);

const viewTicket = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalDetalles.value = true;
};

const editTicket = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalEditar.value = true;
};

const deleteTicket = async (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalEliminar.value = true;
};

const loadTickets = async () => {
    try {
        const response = await axios.get("/user-tickets");
        const allTickets = response.data.map((ticket) => ({
            id: ticket.id,
            tic_titulo: ticket.tic_titulo,
            tic_descripcion: ticket.tic_descripcion,
            pri_id: ticket.pri_id,
            pri_nombre: ticket.prioridad ? ticket.prioridad.pri_nombre : "",
            cat_id: ticket.cat_id,
            cat_nombre: ticket.categoria ? ticket.categoria.cat_nombre : "",
            pab_id: ticket.pab_id,
            pab_nombre: ticket.pabellon ? ticket.pabellon.pab_nombre : "",
            aul_id: ticket.aul_id,
            aul_numero: ticket.aula ? ticket.aula.aul_numero : "",
            tic_estado: ticket.tic_estado,
        }));

        tickets.value.open = allTickets.filter(
            (ticket) => ticket.tic_estado === "Abierto"
        );
        tickets.value.inProgress = allTickets.filter(
            (ticket) => ticket.tic_estado === "En progreso"
        );
        tickets.value.closed = allTickets.filter(
            (ticket) => ticket.tic_estado === "Cerrado"
        );
    } catch (error) {
        console.error("Error al cargar tickets:", error);
    }
};

const fetchPrioridades = async () => {
    try {
        const response = await axios.get("/prioridades");
        prioridades.value = response.data
            .filter((prioridad) => prioridad.pri_activo)
            .map((prioridad) => ({
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

const fetchCategorias = async () => {
    try {
        const response = await axios.get("/categorias");
        categorias.value = response.data
            .filter((categoria) => categoria.cat_activo)
            .map((categoria) => ({
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
        pabellones.value = response.data
            .filter((pabellon) => pabellon.pab_activo)
            .map((pabellon) => ({
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
        aulas.value = response.data
            .filter((aula) => aula.aul_activo)
            .map((aula) => ({
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
];

formFieldsVer.value = [
    { name: "tic_titulo", label: "Título", type: "text" },
    { name: "pri_nombre", label: "Prioridad", type: "text" },
    { name: "cat_nombre", label: "Categoría", type: "text" },
    { name: "pab_nombre", label: "Pabellón", type: "text" },
    { name: "aul_numero", label: "Aula", type: "text" },
    { name: "tic_descripcion", label: "Descripción", type: "textarea" },
    { name: "tic_estado", label: "Estado", type: "text" },
];

const fetchAllData = async () => {
    try {
        await Promise.all([
            loadTickets(),
            fetchPrioridades(),
            fetchCategorias(),
            fetchPabellones(),
            fetchAulas(),
        ]);
    } catch (error) {
        console.error("Error al cargar los datos:", error);
    }
};

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/user-tickets/${itemSeleccionado.value.id}/eliminar`
            );
            await loadTickets();
            mostrarModalEliminar.value = false;
        } catch (error) {
            console.error("Error al eliminar al ticket:", error);
        }
    }
};

onMounted(() => {
    fetchAllData();
});

const showTickets = (status) => {
    activeTab.value = status;
};

const cerrarDetallesModal = () => {
    mostrarModalDetalles.value = false;
};

const cerrarEditarModal = () => {
    mostrarModalEditar.value = false;
};

const cerrarEliminarModal = () => {
    mostrarModalEliminar.value = false;
};
</script>

<template>
    <div class="p-6 flex justify-center">
        <div
            class="shadow-custom bg-white rounded-lg shadow-lg p-6 w-full max-w-6xl"
        >
            <h1
                class="mb-2 text-sm font-bold text-gray-500 sm:text-lg md:text-xl"
            >
                Mis Tickets
            </h1>

            <div class="overflow-x-auto pl-1 pt-5 pb-1 flex justify-left">
                <button
                    :class="[
                        'mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-3 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
                        activeTab === 'open'
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-[#2EBAA1]',
                    ]"
                    @click="showTickets('open')"
                >
                    Mis Tickets Recientes
                </button>
                <button
                    :class="[
                        'mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-3 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
                        activeTab === 'in-progress'
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-[#2EBAA1]',
                    ]"
                    @click="showTickets('in-progress')"
                >
                    Mis Tickets En progreso
                </button>
                <button
                    :class="[
                        'mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-3 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
                        activeTab === 'closed'
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-[#2EBAA1]',
                    ]"
                    @click="showTickets('closed')"
                >
                    Mis Tickets Cerrados
                </button>
            </div>

            <div class="overflow-x-auto">
                <div v-if="activeTab === 'open'">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(ticket, index) in tickets.open"
                                :key="ticket.id"
                                class="transition-colors duration-200 border-b hover:bg-gray-100"
                            >
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:text-sm text-base space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="viewTicket(ticket)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                        title="Ver detalles"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button
                                        @click="editTicket(ticket)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                                        title="Editar"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        @click="deleteTicket(ticket)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                                        title="Eliminar"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="activeTab === 'in-progress'">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(ticket, index) in tickets.inProgress"
                                :key="ticket.id"
                                class="transition-colors duration-200 border-b hover:bg-gray-100"
                            >
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:text-sm text-base space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="viewTicket(ticket)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                        title="Ver detalles"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="activeTab === 'closed'">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(ticket, index) in tickets.closed"
                                :key="ticket.id"
                                class="transition-colors duration-200 border-b hover:bg-gray-100"
                            >
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ index + 1 }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-base"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:text-sm text-base space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="viewTicket(ticket)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                        title="Ver detalles"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <ModalVer
                v-if="mostrarModalDetalles"
                :item="itemSeleccionado"
                itemName="Ticket"
                :formFieldsVer="formFieldsVer"
                :mostrarModalDetalles="mostrarModalDetalles"
                @close="cerrarDetallesModal"
            />

            <ModalEditar
                v-if="mostrarModalEditar"
                :item="itemSeleccionado"
                itemName="Ticket"
                :formFields="formFields"
                :prioridads="prioridades"
                :categorias="categorias"
                :pabellons="pabellones"
                :aulas="aulas"
                :mostrarModalEditar="mostrarModalEditar"
                endpoint="/user-tickets"
                @cerrar="cerrarEditarModal"
                @update="loadTickets"
            />

            <ModalEliminar
                v-if="mostrarModalEliminar"
                :item="itemSeleccionado"
                itemName="Ticket"
                fieldName="tic_titulo"
                @cancelar="cerrarEliminarModal"
                @confirmar="eliminarItem"
            />
        </div>
    </div>
</template>

<style scoped>
.shadow-custom {
    box-shadow: 0 5px 8px rgba(0, 0, 0, 0.5);
}
</style>
