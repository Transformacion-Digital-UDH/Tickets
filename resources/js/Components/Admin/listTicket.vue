<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import filterTicketState from "@/Components/filterTicketState.vue";
import CardTickets from "@/Components/CardTickets.vue";
import ModalCrear from "@/Components/ModalCrear.vue";
import ModalAsignar from "@/Components/ModalAsignar.vue";
import ModalVer from "@/Components/ModalVer.vue";
import ModalEditar from "@/Components/ModalEditar.vue";
import ModalEliminar from "@/Components/ModalEliminar.vue";
import ModalCerrar from "@/Components/ModalCerrar.vue";
import ModalAbrir from "@/Components/ModalAbrir.vue";
import ButtonNuevo from "@/Components/ButtonNuevo.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import {
    faThLarge,
    faTable,
    faFilter,
    faTimes,
} from "@fortawesome/free-solid-svg-icons";
import axios from "axios";

library.add(faThLarge, faTable, faFilter, faTimes);

const tickets = ref([]);
const prioridades = ref([]);
const soportes = ref([]);
const usuarios = ref([]);
const categorias = ref([]);
const pabellones = ref([]);
const aulas = ref([]);
const archivadoActivo = ref(false);
const formFields = ref([]);
const formFieldsAsignar = ref([]);
const formFieldsVer = ref([]);
const buscarQuery = ref("");
const isCardView = ref(true);
const isMobile = ref(false);
const mostrarModalCrear = ref(false);
const mostrarModalAsignar = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const mostrarModalCerrar = ref(false);
const mostrarModalAbrir = ref(false);
const itemSeleccionado = ref(null);
const estadoFilters = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(6);
const totalPages = ref(1);

const toggleViewMode = (isCard) => {
    isCardView.value = isCard;
    localStorage.setItem("isCardView", isCardView.value);
};

const getViewModeFromLocalStorage = () => {
    const storedValue = localStorage.getItem("isCardView");

    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        isCardView.value = true;
    } else {
        isCardView.value = storedValue === "true";
    }
};

const toggleArchivedFilter = () => {
    archivadoActivo.value = !archivadoActivo.value;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const resetArchivedFilter = () => {
    archivadoActivo.value = false;
    localStorage.setItem("archivadoActivo", archivadoActivo.value);
};

const getArchivedFilterFromLocalStorage = () => {
    const storedValue = localStorage.getItem("archivadoActivo");
    archivadoActivo.value = storedValue === "true";
};

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
    if (isMobile.value) {
        isCardView.value = true;
    } else {
        getViewModeFromLocalStorage();
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener("resize", handleResize);
    getArchivedFilterFromLocalStorage();
    fetchAllData();
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const filtrarTickets = computed(() => {
    let filteredTickets = tickets.value;

    if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredTickets = filteredTickets.filter((ticket) => {
            return (
                (ticket.tic_titulo &&
                    ticket.tic_titulo.toLowerCase().includes(query)) ||
                (ticket.soporte_nombre &&
                    ticket.soporte_nombre.toLowerCase().includes(query)) ||
                (ticket.tic_estado &&
                    ticket.tic_estado.toLowerCase().includes(query)) ||
                (ticket.pri_nombre &&
                    ticket.pri_nombre.toLowerCase().includes(query)) ||
                (ticket.cat_nombre &&
                    ticket.cat_nombre.toLowerCase().includes(query))
            );
        });
    }

    if (archivadoActivo.value) {
        filteredTickets = filteredTickets.filter(
            (ticket) => ticket.tic_activo === 0
        );
    } else {
        filteredTickets = filteredTickets.filter(
            (ticket) => ticket.tic_activo === 1
        );
    }

    return filteredTickets.filter((ticket) => {
        const estadoMatch = estadoFilters.value.length
            ? estadoFilters.value.includes(ticket.tic_estado)
            : true;
        return estadoMatch;
    });
});

const updateEstadoFilters = (newFilters) => {
    estadoFilters.value = newFilters;
};

const mapTicketData = (ticket, index, totalTickets) => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    return {
        id: ticket.id,
        tic_titulo: ticket.tic_titulo,
        tic_descripcion: ticket.tic_descripcion,
        tic_archivo: ticket.tic_archivo,
        pri_id: ticket.pri_id,
        pri_nombre: ticket.prioridad ? ticket.prioridad.pri_nombre : "",
        use_id: ticket.use_id,
        name: ticket.user ? ticket.user.name : "",
        sop_id: ticket.soporte_actual ? ticket.soporte_actual.sop_id : null,
        soporte_nombre:
            ticket.soporte_actual && ticket.soporte_actual.soporte
                ? ticket.soporte_actual.soporte.name
                : "No asignado",
        cat_id: ticket.cat_id,
        cat_nombre: ticket.categoria ? ticket.categoria.cat_nombre : "",
        pab_id: ticket.pab_id,
        pab_nombre: ticket.pabellon ? ticket.pabellon.pab_nombre : "",
        aul_id: ticket.aul_id,
        aul_numero: ticket.aula ? ticket.aula.aul_numero : "",
        tic_estado: ticket.tic_estado,
        tic_activo: ticket.tic_activo,
        created_at: ticket.created_at,
        updated_at: ticket.updated_at,
        row_number: totalTickets - (startIndex + index),
    };
};

const changePage = (pageNumber) => {
    currentPage.value = pageNumber;
    fetchTickets(pageNumber);
};

const fetchTickets = async (page = 1) => {
    try {
        const response = await axios.get(`/ticketspaginated?page=${page}`);

        const totalTickets = response.data.total;
        const ticketData = response.data.data.map((ticket, index) =>
            mapTicketData(ticket, index, totalTickets)
        );

        tickets.value = ticketData;
        currentPage.value = response.data.current_page;
        itemsPerPage.value = response.data.per_page;
        totalPages.value = response.data.last_page;
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

const fetchSoportes = async () => {
    try {
        const response = await axios.get("/soportes");
        soportes.value = response.data
            .filter((soporte) => soporte.activo)
            .map((soporte) => ({
                value: soporte.id,
                text: soporte.name,
            }));
        formFieldsAsignar.value = formFieldsAsignar.value.map((field) => {
            if (field.name == "sop_id") {
                return {
                    ...field,
                    options: soportes.value,
                };
            }
            return field;
        });
        formFieldsAsignar.value = [...formFieldsAsignar.value];
    } catch (error) {
        console.error("Error al cargar los soportes técnicos:", error);
    }
};

const fetchUsuarios = async () => {
    try {
        const response = await axios.get("/usuarios");
        usuarios.value = response.data
            .filter((usuario) => usuario.activo)
            .map((usuario) => ({
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
    { name: "tic_titulo", label: "Título", type: "text", required: true },
    {
        name: "pri_id",
        label: "Prioridad",
        type: "select",
        required: true,
        options: prioridades.value,
    },
    {
        name: "use_id",
        label: "Usuario",
        type: "select",
        required: true,
        options: usuarios.value,
    },
    {
        name: "cat_id",
        label: "Categoría",
        type: "select",
        required: true,
        options: categorias.value,
    },
    {
        name: "pab_id",
        label: "Pabellón",
        type: "select",
        required: true,
        options: pabellones.value,
    },
    {
        name: "aul_id",
        label: "Aula",
        type: "select",
        required: true,
        options: aulas.value,
    },
    {
        name: "tic_descripcion",
        label: "Descripción",
        type: "textarea",
        required: true,
    },
    {
        name: "tic_archivo",
        label: "Imágenes",
        type: "file",
        required: false,
    },
    { name: "tic_activo", label: "Activo", type: "boolean" },
];

formFieldsAsignar.value = [
    {
        name: "sop_id",
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
    { name: "tic_archivo", label: "Imágenes", type: "file" },
];

const cerrarTicket = async (ticket) => {
    if (ticket) {
        try {
            const response = await axios.put(
                `/tickets/${ticket.id}/updateEstado`,
                { tic_estado: "Cerrado" },
                {
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            );
            if (response.status === 200) {
                toast.success("Ticket cerrado correctamente", {
                    position: "bottom-right",
                    autoClose: 3000,
                });
                await fetchTickets();
                mostrarModalCerrar.value = false;
            }
        } catch (error) {
            console.error("Error al cerrar el ticket:", error.response?.data);
            toast.error("Error al cerrar el ticket", {
                position: "bottom-right",
                autoClose: 3000,
            });
        }
    }
};

const abrirTicket = async (ticket) => {
    if (ticket) {
        try {
            const response = await axios.put(
                `/tickets/${ticket.id}/updateEstado`,
                { tic_estado: "Reabierto" },
                {
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                }
            );
            if (response.status === 200) {
                toast.success("Ticket reabierto correctamente", {
                    position: "bottom-right",
                    autoClose: 3000,
                });
                await fetchTickets();
                mostrarModalAbrir.value = false;
            }
        } catch (error) {
            console.error("Error al reabrir el ticket:", error.response?.data);
            toast.error("Error al reabrir el ticket", {
                position: "bottom-right",
                autoClose: 3000,
            });
        }
    }
};

const eliminarItem = async () => {
    if (itemSeleccionado.value) {
        try {
            await axios.delete(
                `/tickets/${itemSeleccionado.value.id}/eliminar`
            );
            await fetchTickets();
            mostrarModalEliminar.value = false;
            alertaEliminar();
        } catch (error) {
            toast.error(
                "No puedes eliminar este ticket, por el momento solo desactivelo",
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

const alertaEliminar = () => {
    fetchTickets();
    toast.success("Ticket eliminado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-4",
    });
};

const cerrarCrearModal = async () => {
    mostrarModalCrear.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirAsignarModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalAsignar.value = true;
};

const cerrarAsignarModal = async () => {
    mostrarModalAsignar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirDetallesModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalDetalles.value = true;
};

const cerrarDetallesModal = async () => {
    mostrarModalDetalles.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirEditarModal = async (ticket) => {
    itemSeleccionado.value = ticket;
    await fetchPrioridades();
    await fetchUsuarios();
    await fetchCategorias();
    await fetchPabellones();
    await fetchAulas();
    mostrarModalEditar.value = true;
};

const cerrarEditarModal = async () => {
    mostrarModalEditar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirEliminarModal = (ticket) => {
    itemSeleccionado.value = ticket;
    mostrarModalEliminar.value = true;
};

const cerrarEliminarModal = async () => {
    mostrarModalEliminar.value = false;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirCerrarModal = async (ticket) => {
    itemSeleccionado.value = ticket;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
    mostrarModalCerrar.value = true;
};

const cerrarCerrarModal = async () => {
    mostrarModalCerrar.value = false;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const abrirAbrirModal = async (ticket) => {
    itemSeleccionado.value = ticket;
    currentPage.value = 1;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
    mostrarModalAbrir.value = true;
};

const cerrarAbrirModal = async () => {
    mostrarModalAbrir.value = false;
    localStorage.setItem("currentPage", 1);
    await fetchTickets(currentPage.value);
};

const fetchAllData = async () => {
    try {
        await Promise.all([
            fetchTickets(),
            fetchPrioridades(),
            fetchSoportes(),
            fetchUsuarios(),
            fetchCategorias(),
            fetchPabellones(),
            fetchAulas(),
        ]);
    } catch (error) {
        console.error("Error al cargar los datos:", error);
    }
};

const handleAsign = (ticket) => {
    abrirAsignarModal(ticket);
};

const handleView = (ticket) => {
    abrirDetallesModal(ticket);
};

const handleEdit = (ticket) => {
    abrirEditarModal(ticket);
};

const handleEliminar = (ticket) => {
    abrirEliminarModal(ticket);
};

const handleCerrar = (ticket) => {
    abrirCerrarModal(ticket);
};

const handleAbrir = (ticket) => {
    abrirAbrirModal(ticket);
};

const getEstadoLabelClass = (estado) => {
    switch (estado) {
        case "Abierto":
            return "bg-orange-100 text-orange-800";
        case "Asignado":
            return "bg-gray-100 text-gray-800";
        case "En progreso":
            return "bg-blue-100 text-blue-800";
        case "Resuelto":
            return "bg-green-100 text-green-800";
        case "Cerrado":
            return "bg-red-100 text-red-800";
        case "Reabierto":
            return "bg-yellow-100 text-yellow-800";
        default:
            return "bg-purple-100 text-purple-800";
    }
};
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Lista de Tickets
        </h1>
        <div
            class="flex flex-col items-center justify-between mb-2 sm:flex-row"
        >
            <div
                class="relative w-full mb-2 sm:w-auto sm:mb-0 flex items-center"
            >
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
                <button
                    @click="toggleArchivedFilter"
                    class="ml-2 flex items-center px-2 py-1 rounded-md hover:bg-opacity-80 font-semibold"
                    :class="
                        archivadoActivo
                            ? 'bg-red-500 text-white'
                            : 'bg-[#2EBAA1] text-white'
                    "
                >
                    <i class="fas fa-filter mr-1"></i>
                    <span>{{ archivadoActivo ? "Archivado" : "Activo" }}</span>
                    <i
                        class="fas fa-times ml-2"
                        v-if="archivadoActivo"
                        @click.stop="resetArchivedFilter"
                    ></i>
                </button>
            </div>
            <div class="flex items-center space-x-1">
                <font-awesome-icon
                    v-if="!isMobile"
                    @click="toggleViewMode(true)"
                    :class="[
                        'cursor-pointer flex items-center px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg shadow-sm',
                        isCardView
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-gray-600',
                    ]"
                    icon="th-large"
                />
                <font-awesome-icon
                    v-if="!isMobile"
                    @click="toggleViewMode(false)"
                    :class="[
                        'cursor-pointer flex items-center px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg shadow-sm',
                        !isCardView
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-gray-600',
                    ]"
                    icon="table"
                />
            </div>
            <div class="w-full sm:w-auto">
                <ButtonNuevo
                    @click="mostrarModalCrear = true"
                    class="w-full sm:w-auto"
                />
            </div>
        </div>
        <filterTicketState
            :estadoFilters="estadoFilters"
            @update-filters="updateEstadoFilters"
        />

        <div v-if="isCardView">
            <CardTickets
                :tickets="filtrarTickets"
                :currentPage="currentPage"
                :totalPages="totalPages"
                @open="handleAbrir"
                @close="handleCerrar"
                @asign="handleAsign"
                @view="handleView"
                @edit="handleEdit"
                @eliminar="handleEliminar"
                @changePage="changePage"
            />
        </div>

        <div v-else>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                N°
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Categoría
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Título
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Soporte Técnico
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Prioridad
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Estado
                            </th>
                            <th
                                class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="ticket in filtrarTickets"
                            :key="ticket.id"
                            class="transition-colors duration-200 border-b hover:bg-gray-100"
                        >
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ ticket.row_number }}
                            </td>
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ ticket.cat_nombre }}
                            </td>
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ ticket.tic_titulo }}
                            </td>
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ ticket.soporte_nombre }}
                            </td>
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ ticket.pri_nombre }}
                            </td>
                            <td
                                class="px-2 py-2 text-xs sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                <span
                                    :class="[
                                        'px-2 py-1 text-xs font-semibold rounded-full sm:text-xs md:text-sm',
                                        getEstadoLabelClass(ticket.tic_estado),
                                    ]"
                                >
                                    {{ ticket.tic_estado }}
                                </span>
                            </td>
                            <td
                                class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                            >
                                <button
                                    v-if="ticket.tic_estado === 'Cerrado'"
                                    @click="handleAbrir(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-green-300 to-green-500 hover:from-green-400 hover:to-green-600 flex items-center space-x-2"
                                    title="Reabrir Ticket"
                                >
                                    <i class="fa-solid fa-lock-open"></i>
                                </button>
                                <button
                                    v-if="
                                        ticket.tic_estado === 'Abierto' ||
                                        ticket.tic_estado === 'Asignado' ||
                                        ticket.tic_estado === 'Reabierto'
                                    "
                                    @click="handleCerrar(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-purple-300 to-purple-500 hover:from-purple-400 hover:to-purple-600 flex items-center space-x-2"
                                    title="Cerrar Ticket"
                                >
                                    <i class="fa-solid fa-lock"></i>
                                </button>
                                <button
                                    v-if="
                                        ticket.tic_estado === 'Abierto' ||
                                        ticket.tic_estado === 'Asignado' ||
                                        ticket.tic_estado === 'Reabierto'
                                    "
                                    @click="handleAsign(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-blue-300 to-blue-500 hover:from-blue-400 hover:to-blue-600"
                                    title="Asignar Soporte"
                                >
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                <button
                                    @click="handleView(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                    title="Ver detalles"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    @click="handleEdit(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                                    title="Editar"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button
                                    @click="handleEliminar(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                                    title="Eliminar"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filtrarTickets.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-3 text-xs text-center text-gray-500 sm:text-sm"
                            >
                                No se encontraron resultados.
                            </td>
                        </tr>
                    </tbody>
                </table>
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

        <ModalCrear
            v-if="mostrarModalCrear"
            :formFields="formFields"
            :prioridads="prioridades"
            :usuarios="usuarios"
            :categorias="categorias"
            :pabellons="pabellones"
            :aulas="aulas"
            itemName="Ticket"
            endpoint="/tickets"
            @cerrar="cerrarCrearModal"
            @crear="fetchTickets"
        />

        <ModalAsignar
            v-if="mostrarModalAsignar"
            :formFieldsAsignar="formFieldsAsignar"
            :soportes="soportes"
            :ticketId="itemSeleccionado?.id || null"
            :selectedSoporteId="itemSeleccionado?.sop_id || null"
            itemName="Soporte Técnico"
            endpoint="/asignar"
            @cerrar="cerrarAsignarModal"
            @crear="fetchTickets"
            @actualizar="fetchTickets"
        />

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
            :usuarios="usuarios"
            :categorias="categorias"
            :pabellons="pabellones"
            :aulas="aulas"
            :mostrarModalEditar="mostrarModalEditar"
            endpoint="/tickets"
            @cerrar="cerrarEditarModal"
            @update="fetchTickets"
        />

        <ModalEliminar
            v-if="mostrarModalEliminar"
            :item="itemSeleccionado"
            itemName="Ticket"
            fieldName="tic_titulo"
            @cancelar="cerrarEliminarModal"
            @confirmar="eliminarItem"
        />

        <ModalCerrar
            v-if="mostrarModalCerrar"
            :item="itemSeleccionado"
            itemName="Ticket"
            fieldName="tic_titulo"
            @cancelar="cerrarCerrarModal"
            @close="cerrarTicket(itemSeleccionado)"
        />

        <ModalAbrir
            v-if="mostrarModalAbrir"
            :item="itemSeleccionado"
            itemName="Ticket"
            fieldName="tic_titulo"
            @cancelar="cerrarAbrirModal"
            @open="abrirTicket(itemSeleccionado)"
        />
    </div>
</template>
