<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";

const storedTab = localStorage.getItem("activeTab") || "open";
const activeTab = ref(storedTab);

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

const currentPage = ref(1);
const itemsPerPage = ref(6);
const totalPages = ref(1);

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

const mapTicketData = (ticket, index, totalTickets) => ({
    id: ticket.id,
    tic_titulo: ticket.tic_titulo,
    tic_descripcion: ticket.tic_descripcion,
    pri_id: ticket.pri_id,
    pri_nombre: ticket.prioridad?.pri_nombre || "No disponible",
    cat_id: ticket.cat_id,
    cat_nombre: ticket.categoria?.cat_nombre || "No disponible",
    pab_id: ticket.pab_id,
    pab_nombre: ticket.pabellon?.pab_nombre || "No disponible",
    aul_id: ticket.aul_id,
    aul_numero: ticket.aula?.aul_numero || "No disponible",
    tic_estado: ticket.tic_estado,
    row_number:
        totalTickets - ((currentPage.value - 1) * itemsPerPage.value + index),
});

const loadTickets = async (page = 1) => {
    try {
        const estado =
            activeTab.value === "open"
                ? "Abierto"
                : activeTab.value === "in-progress"
                ? "En progreso"
                : "Cerrado";

        const response = await axios.get(
            `/user-tickets?page=${page}&estado=${estado}`
        );

        const totalTickets = response.data.total;
        const ticketData = response.data.data
            .filter((ticket) => ticket.tic_activo)
            .map((ticket, index) => mapTicketData(ticket, index, totalTickets));

        currentPage.value = response.data.current_page;
        itemsPerPage.value = response.data.per_page;
        totalPages.value = response.data.last_page;

        if (activeTab.value === "open") {
            tickets.value.open = ticketData;
        } else if (activeTab.value === "in-progress") {
            tickets.value.inProgress = ticketData;
        } else if (activeTab.value === "closed") {
            tickets.value.closed = ticketData;
        }
    } catch (error) {
        console.error(
            "Error al cargar los tickets:",
            error?.response?.data?.message || error.message
        );
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
            toast.success("Su ticket se ha eliminado correctamente", {
                autoClose: 3000,
                position: "bottom-right",
                style: {
                    width: "400px",
                },
                className: "border-l-4 border-green-500 p-2",
            });
        } catch (error) {
            toast.error("Hubo un error al eliminar el ticket", {
                autoClose: 3000,
                position: "bottom-right",
                style: {
                    width: "400px",
                },
                className: "border-l-4 border-green-500 p-2",
            });
        }
    }
};

const changePage = (pageNumber) => {
    currentPage.value = pageNumber;
    loadTickets(pageNumber);
};

onMounted(() => {
    fetchAllData();
});

const showTickets = (status) => {
    activeTab.value = status;
    localStorage.setItem("activeTab", status);
    loadTickets(1);
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
            class="shadow-custom bg-white rounded-lg shadow-lg p-4 w-full max-w-6xl"
        >
            <h1
                class="mb-2 text-sm font-bold text-gray-500 sm:text-lg md:text-xl"
            >
                Mis Tickets
            </h1>

            <div
                class="overflow-x-auto pl-1 pt-5 pb-1 flex justify-left flex-wrap"
            >
                <button
                    :class="[
                        'mr-2 mb-3 w-full sm:w-auto flex justify-center items-center px-4 py-2 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
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
                        'mr-2 mb-3 w-full sm:w-auto flex justify-center items-center px-4 py-2 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
                        activeTab === 'in-progress'
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-[#2EBAA1]',
                    ]"
                    @click="showTickets('in-progress')"
                >
                    Mis Tickets En Progreso
                </button>
                <button
                    :class="[
                        'mr-2 mb-3 w-full sm:w-auto flex justify-center items-center px-4 py-2 text-xs sm:text-sm font-semibold transition-all duration-300 rounded-lg shadow',
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
                <div class="hidden sm:block" v-if="activeTab === 'open'">
                    <table
                        class="min-w-full divide-y divide-gray-200 table-auto"
                    >
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
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
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.row_number }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-center space-x-2"
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
                    <div class="mt-4 flex justify-center">
                        <button
                            v-for="page in Array.from(
                                { length: totalPages },
                                (_, i) => i + 1
                            )"
                            :key="page"
                            :class="[
                                currentPage === page
                                    ? 'bg-[#2EBAA1] text-white'
                                    : 'bg-white text-[#2EBAA1]',
                                'mx-2 px-3 py-1 rounded-lg',
                            ]"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>

                <div class="block sm:hidden" v-if="activeTab === 'open'">
                    <div
                        v-for="(ticket, index) in tickets.open"
                        :key="ticket.id"
                        class="relative border rounded-lg p-4 mb-4 shadow-sm"
                    >
                        <p
                            class="absolute top-0 right-0 mt-2 mr-2 px-3 py-1 text-xs font-bold text-white uppercase bg-green-500 rounded-full"
                        >
                            {{ ticket.row_number }}
                        </p>

                        <p class="text-sm font-semibold text-gray-700">
                            Título: {{ ticket.tic_titulo }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Descripción: {{ ticket.tic_descripcion }}
                        </p>
                        <div class="flex space-x-4 mt-3">
                            <button
                                @click="viewTicket(ticket)"
                                class="text-teal-600 hover:text-teal-800"
                                title="Ver detalles"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                            <button
                                @click="editTicket(ticket)"
                                class="text-green-600 hover:text-green-800"
                                title="Editar"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button
                                @click="deleteTicket(ticket)"
                                class="text-red-600 hover:text-red-800"
                                title="Eliminar"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-center">
                        <button
                            v-for="page in Array.from(
                                { length: totalPages },
                                (_, i) => i + 1
                            )"
                            :key="page"
                            :class="[
                                currentPage === page
                                    ? 'bg-[#2EBAA1] text-white'
                                    : 'bg-white text-[#2EBAA1]',
                                'mx-2 px-3 py-1 rounded-lg',
                            ]"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>

                <div class="hidden sm:block" v-if="activeTab === 'in-progress'">
                    <table
                        class="min-w-full divide-y divide-gray-200 table-auto"
                    >
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
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
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.row_number }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-center space-x-2"
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
                    <div class="mt-4 flex justify-center">
                        <button
                            v-for="page in Array.from(
                                { length: totalPages },
                                (_, i) => i + 1
                            )"
                            :key="page"
                            :class="[
                                currentPage === page
                                    ? 'bg-[#2EBAA1] text-white'
                                    : 'bg-white text-[#2EBAA1]',
                                'mx-2 px-3 py-1 rounded-lg',
                            ]"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>

                <div class="block sm:hidden" v-if="activeTab === 'in-progress'">
                    <div
                        v-for="(ticket, index) in tickets.inProgress"
                        :key="ticket.id"
                        class="relative border rounded-lg p-4 mb-4 shadow-sm"
                    >
                        <p
                            class="absolute top-0 right-0 mt-2 mr-2 px-3 py-1 text-xs font-bold text-white uppercase bg-green-500 rounded-full"
                        >
                            {{ ticket.row_number }}
                        </p>

                        <p class="text-sm font-semibold text-gray-700">
                            Título: {{ ticket.tic_titulo }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Descripción: {{ ticket.tic_descripcion }}
                        </p>

                        <div class="flex space-x-4 mt-3">
                            <button
                                @click="viewTicket(ticket)"
                                class="text-teal-600 hover:text-teal-800"
                                title="Ver detalles"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="mt-4 flex justify-center">
                            <button
                                v-for="page in Array.from(
                                    { length: totalPages },
                                    (_, i) => i + 1
                                )"
                                :key="page"
                                :class="[
                                    currentPage === page
                                        ? 'bg-[#2EBAA1] text-white'
                                        : 'bg-white text-[#2EBAA1]',
                                    'mx-2 px-3 py-1 rounded-lg',
                                ]"
                                @click="changePage(page)"
                            >
                                {{ page }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="hidden sm:block" v-if="activeTab === 'closed'">
                    <table
                        class="min-w-full divide-y divide-gray-200 table-auto"
                    >
                        <thead class="bg-white">
                            <tr>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    N°
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Título
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    Descripción
                                </th>
                                <th
                                    class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm"
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
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.row_number }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_titulo }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm"
                                >
                                    {{ ticket.tic_descripcion }}
                                </td>
                                <td
                                    class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm text-center space-x-2"
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
                    <div class="mt-4 flex justify-center">
                        <button
                            v-for="page in Array.from(
                                { length: totalPages },
                                (_, i) => i + 1
                            )"
                            :key="page"
                            :class="[
                                currentPage === page
                                    ? 'bg-[#2EBAA1] text-white'
                                    : 'bg-white text-[#2EBAA1]',
                                'mx-2 px-3 py-1 rounded-lg',
                            ]"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>
                <div class="block sm:hidden" v-if="activeTab === 'closed'">
                    <div
                        v-for="(ticket, index) in tickets.closed"
                        :key="ticket.id"
                        class="relative border rounded-lg p-4 mb-4 shadow-sm"
                    >
                        <p
                            class="absolute top-0 right-0 mt-2 mr-2 px-3 py-1 text-xs font-bold text-white uppercase bg-green-500 rounded-full"
                        >
                            {{ ticket.row_number }}
                        </p>

                        <p class="text-sm font-semibold text-gray-700">
                            Título: {{ ticket.tic_titulo }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Descripción: {{ ticket.tic_descripcion }}
                        </p>

                        <div class="flex space-x-4 mt-3">
                            <button
                                @click="viewTicket(ticket)"
                                class="text-teal-600 hover:text-teal-800"
                                title="Ver detalles"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-center">
                        <button
                            v-for="page in Array.from(
                                { length: totalPages },
                                (_, i) => i + 1
                            )"
                            :key="page"
                            :class="[
                                currentPage === page
                                    ? 'bg-[#2EBAA1] text-white'
                                    : 'bg-white text-[#2EBAA1]',
                                'mx-2 px-3 py-1 rounded-lg',
                            ]"
                            @click="changePage(page)"
                        >
                            {{ page }}
                        </button>
                    </div>
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
