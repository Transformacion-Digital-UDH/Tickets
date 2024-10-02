<script setup>
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
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
const isMobile = ref(false);
const mostrarModalCrear = ref(false);
const mostrarModalAsignar = ref(false);
const mostrarModalDetalles = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalEliminar = ref(false);
const itemSeleccionado = ref(null);
const estadoFilters = ref([]);
const showEstadoDropdown = ref(false);

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
    if (isMobile.value) {
        isCardView.value = true;
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const headers = [
    "Título",
    "Categoría",
    "Descripción",
    "Prioridad",
    "Estado",
    "Soporte",
];

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

    return filteredTickets.filter((ticket) => {
        const estadoMatch = estadoFilters.value.length
            ? estadoFilters.value.includes(ticket.tic_estado)
            : true;
        return estadoMatch;
    });
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
        }));
    } catch (error) {
        console.error("Error al cargar los tickets:", error);
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
];

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

const toggleEstadoDropdown = () => {
    showEstadoDropdown.value = !showEstadoDropdown.value;
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

onMounted(() => {
    fetchAllData();
});

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
            <div class="flex items-center space-x-1">
                <font-awesome-icon
                    v-if="!isMobile"
                    @click="isCardView = true"
                    :class="[
                        'flex items-center px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg shadow-sm',
                        isCardView
                            ? 'bg-[#2EBAA1] text-white'
                            : 'bg-white text-gray-600',
                    ]"
                    icon="th-large"
                />
                <font-awesome-icon
                    v-if="!isMobile"
                    @click="isCardView = false"
                    :class="[
                        'flex items-center px-4 py-2 text-sm font-semibold border border-gray-300 rounded-lg shadow-sm',
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
        <div class="relative text-right mb-2 mr-1">
                <i
                    class="fas fa-sliders-h cursor-pointer text-gray-500"
                    @click="toggleEstadoDropdown"
                ></i>
                <div
                    v-if="showEstadoDropdown"
                    class="absolute right-0 z-10 mt-2 w-48 bg-white border border-gray-300 shadow-md rounded-md p-2"
                >
                    <div class="grid grid-cols-1 gap-2">
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="Abierto"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >Abierto</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="Asignado"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >Asignado</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="En progreso"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >En progreso</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="Resuelto"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >Resuelto</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="Cerrado"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >Cerrado</span
                            >
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="checkbox"
                                v-model="estadoFilters"
                                value="Reabierto"
                                class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                            />
                            <span class="ml-2 text-gray-700 text-sm"
                                >Reabierto</span
                            >
                        </label>
                    </div>
                </div>
            </div>

        <div v-if="isCardView">
            <CardTickets
                :tickets="filtrarTickets"
                @asign="handleAsign"
                @view="handleView"
                @edit="handleEdit"
                @eliminar="handleEliminar"
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
                            v-for="(ticket, index) in filtrarTickets"
                            :key="ticket.id"
                            class="transition-colors duration-200 border-b hover:bg-gray-100"
                        >
                            <td
                                class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                            >
                                {{ index + 1 }}
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
                                    v-if="
                                        ticket.tic_estado === 'Abierto' ||
                                        ticket.tic_estado === 'Asignado' ||
                                        ticket.tic_estado === 'Reabierto'
                                    "
                                    @click="handleAsign(ticket)"
                                    class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-blue-300 to-blue-500 hover:from-blue-400 hover:to-blue-600"
                                    title="Asignar"
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
    </div>
</template>
