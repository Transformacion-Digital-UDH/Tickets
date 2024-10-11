<script>
import { ref, onMounted, computed, nextTick } from "vue";

export default {
    props: {
        success: String,
    },
    setup() {
        const tickets = ref([]);
        const prioridades = ref([]);
        const categorias = ref([]);
        const isTableView = ref(false);
        const mostrarModal = ref(false); // Control del estado del modal
        const ticketSeleccionado = ref(null); // Ticket seleccionado para el modal
        const buscarQuery = ref("");
        const filtroEstado = ref([]);
        const filtroPrioridad = ref("");
        const filtroCategoria = ref("");
        const dropdownOpen = ref(false);
        const isImageModalOpen = ref(false); // Modal de imagen ampliada
        const selectedImageUrl = ref("");

        // Variables para paginación
        const currentPage = ref(1); // <-- Definimos currentPage
        const ticketsPerPage = ref(10);

        // Función para detectar si es dispositivo móvil
        const isMobile = ref(window.innerWidth < 768);

        const checkIsMobile = () => {
            isMobile.value = window.innerWidth < 768;
            if (isMobile.value) {
                isTableView.value = false;
            }
        };

        window.addEventListener('resize', checkIsMobile);

        onMounted(() => {
            checkIsMobile();
            recargarTickets();
        });

        // Función para abrir el modal y mostrar detalles del ticket
        const abrirModal = (ticket) => {
            ticketSeleccionado.value = { ...ticket };
            mostrarModal.value = true; // Mostrar el modal
        };

        // Función para cerrar el modal
        const cerrarModal = () => {
            mostrarModal.value = false; // Cerrar el modal
        };

        const openImageModal = (url) => {
            selectedImageUrl.value = url;
            isImageModalOpen.value = true;
        };

        const closeImageModal = () => {
            isImageModalOpen.value = false;
            selectedImageUrl.value = "";
        };

        const estadosDisponibles = [
            "Abierto",
            "Asignado",
            "En progreso",
            "Resuelto",
            "Cerrado",
            "Reabierto",
        ];

        const formFieldsVer = ref([
            { label: "Título", name: "tic_titulo" },
            { label: "Descripción", name: "tic_descripcion" },
            { label: "Prioridad", name: "prioridad" },
            { label: "Categoría", name: "categoria" },
            { label: "Estado", name: "tic_estado" },
            { label: "Fecha de Creación", name: "created_at" },
            { label: "Imagen", name: "tic_archivo" },
        ]);

        const toggleView = () => {
            if (!isMobile.value) {
                isTableView.value = !isTableView.value;
            }
        };

        const toggleDropdown = () => {
            dropdownOpen.value = !dropdownOpen.value;
        };

        const paginatedTickets = computed(() => {
            const startIndex = (currentPage.value - 1) * ticketsPerPage.value;
            const endIndex = startIndex + ticketsPerPage.value;
            return filtrarTickets.value.slice(startIndex, endIndex);
        });

        const totalPages = computed(() => {
            return Math.ceil(filtrarTickets.value.length / ticketsPerPage.value);
        });

        const filtrarTickets = computed(() => {
            let filteredTickets = tickets.value;

            if (buscarQuery.value) {
                const query = buscarQuery.value.toLowerCase();
                filteredTickets = filteredTickets.filter((ticket) => {
                    return (
                        (ticket.tic_titulo &&
                            ticket.tic_titulo.toLowerCase().includes(query)) ||
                        (ticket.prioridad &&
                            ticket.prioridad.toLowerCase().includes(query)) ||
                        (ticket.categoria &&
                            ticket.categoria.toLowerCase().includes(query)) ||
                        (ticket.tic_estado &&
                            ticket.tic_estado.toLowerCase().includes(query))
                    );
                });
            }

            if (filtroEstado.value.length) {
                filteredTickets = filteredTickets.filter((ticket) =>
                    filtroEstado.value.includes(ticket.tic_estado)
                );
            }

            if (filtroPrioridad.value) {
                filteredTickets = filteredTickets.filter(
                    (ticket) => ticket.prioridad === filtroPrioridad.value
                );
            }

            if (filtroCategoria.value) {
                filteredTickets = filteredTickets.filter(
                    (ticket) => ticket.categoria === filtroCategoria.value
                );
            }

            return filteredTickets;
        });

        const recargarTickets = async () => {
            try {
                const response = await fetch("support-optener");
                const data = await response.json();

                prioridades.value = [
                    ...new Set(data.map((ticket) => ticket.prioridad)),
                ];
                categorias.value = [
                    ...new Set(data.map((ticket) => ticket.categoria)),
                ];

                tickets.value = data
                    .map((ticket) => ({
                        id: ticket.id,
                        tic_titulo: ticket.tic_titulo,
                        tic_descripcion: ticket.tic_descripcion,
                        tic_archivo: ticket.tic_archivo,
                        tic_estado: ticket.tic_estado,
                        prioridad: ticket.prioridad
                            ? ticket.prioridad.pri_nombre
                            : "N/A",
                        categoria: ticket.categoria
                            ? ticket.categoria.cat_nombre
                            : "N/A",
                        created_at: ticket.created_at,
                    }))
                    .sort(
                        (a, b) =>
                            new Date(b.created_at) - new Date(a.created_at)
                    );
            } catch (error) {
                console.error("Error al recargar los tickets:", error);
            }
        };

        const verDetalles = (ticket) => {
            abrirModal(ticket); // Llamamos a abrirModal para mostrar el modal
        };

        const aceptarTicket = async (ticket) => {
            try {
                const response = await fetch(
                    `/soporte/tickets/aceptar/${ticket.id}`,
                    {
                        method: "PUT",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error("Error al aceptar el ticket");
                }

                const data = await response.json();

                if (data.status) {
                    await recargarTickets();
                    await nextTick();
                } else {
                    console.error(data.msg);
                }
            } catch (error) {
                console.error("Error al aceptar el ticket:", error);
            }
        };

        const finalizarTicket = async (ticket) => {
            try {
                const response = await fetch(
                    `/soporte/tickets/finalizar/${ticket.id}`,
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error("Error al finalizar el ticket");
                }

                const data = await response.json();

                if (data.status) {
                    await recargarTickets();
                    await nextTick();
                } else {
                    console.error(data.msg);
                }
            } catch (error) {
                console.error("Error al finalizar el ticket:", error);
            }
        };

        const goToPage = (page) => {
            currentPage.value = page;
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

        return {
            tickets,
            openImageModal,
            closeImageModal,
            isImageModalOpen,
            selectedImageUrl,
            abrirModal,
            cerrarModal,
            prioridades,
            categorias,
            isTableView,
            toggleView,
            verDetalles,
            mostrarModal,
            ticketSeleccionado,
            formFieldsVer,
            aceptarTicket,
            finalizarTicket,
            buscarQuery,
            filtroEstado,
            filtroPrioridad,
            filtroCategoria,
            filtrarTickets,
            getEstadoLabelClass,
            toggleDropdown,
            dropdownOpen,
            estadosDisponibles,
            isMobile,
            paginatedTickets,
            totalPages,
            goToPage,
            currentPage, // <-- Aquí retornamos currentPage
        };
    },
};
</script>

<template>
    <div class="p-4">
        <h1 class="text-xl font-bold text-left mb-4">Tickets Asignados</h1>

        <!-- Barra de búsqueda y Filtros -->
        <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:justify-between mb-4">
            <!-- Barra de búsqueda -->
            <div class="relative w-full sm:w-auto">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="text-gray-400 fas fa-search"></i>
                </span>
                <input type="text" v-model="buscarQuery" placeholder="Buscar..."
                    class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5" />
            </div>

            <!-- Filtro por Estado con Dropdown y Checkboxes -->
            <div class="relative w-full sm:w-auto">
                <button @click="toggleDropdown"
                    class="flex items-center px-4 py-2 bg-white border rounded-md shadow-md">
                    <i class="fas fa-sliders-h cursor-pointer text-gray-500 mr"></i>
                </button>

                <!-- Dropdown de checkboxes -->
                <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border rounded-md shadow-lg z-10">
                    <div class="p-2">
                        <label v-for="estado in estadosDisponibles" :key="estado"
                            class="flex items-center space-x-2 py-1">
                            <input type="checkbox" v-model="filtroEstado" :value="estado"
                                class="form-checkbox h-4 w-4 text-blue-600" />
                            <span>{{ estado }}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón para cambiar la vista -->
        <div class="text-right mb-4" v-if="!isMobile">
            <button @click="toggleView"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 transition-colors duration-300">
                <i :class="isTableView ? 'fas fa-th-large' : 'fas fa-table'"></i>
            </button>
        </div>

        <!-- Modal de detalles del ticket -->
        <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center bg-gray-400 bg-opacity-30 z-50">
            <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg z-50">
                <div class="p-4 border-2 border-gray-400 rounded-lg">
                    <h2 class="mb-4 text-xl font-bold text-gray-600">Detalles del Ticket</h2>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="py-2 pr-10 text-left text-gray-600 border-b-2 border-gray-400">
                                    Campo
                                </th>
                                <th class="py-2 text-left text-gray-600 border-b-2 border-gray-400">
                                    Valor
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(field, index) in formFieldsVer" :key="index" class="border-b border-gray-400">
                                <td class="py-2 pr-10 font-semibold text-left text-gray-500">
                                    {{ field.label }}
                                </td>
                                <td class="py-2 text-left text-gray-600">
                                    <!-- Mostrar fecha con formato si es 'created_at' -->
                                    <span v-if="field.name === 'created_at'">
                                        {{
                                            ticketSeleccionado[field.name]
                                                ? new Date(ticketSeleccionado[field.name]).toLocaleString()
                                                : "No disponible"
                                        }}
                                    </span>

                                    <!-- Mostrar la imagen si el campo es 'tic_archivo' -->
                                    <span v-else-if="field.name === 'tic_archivo'">
                                        <div v-if="ticketSeleccionado[field.name]">
                                            <img :src="`/storage/${ticketSeleccionado[field.name]}`"
                                                alt="Archivo asociado"
                                                class="object-cover w-32 h-32 border border-gray-300 rounded-lg cursor-pointer"
                                                @click="openImageModal(`/storage/${ticketSeleccionado[field.name]}`)" />
                                        </div>
                                        <div v-else>No disponible</div>
                                    </span>

                                    <!-- Mostrar texto por defecto para otros campos -->
                                    <span v-else>{{ ticketSeleccionado[field.name] || "No disponible" }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex justify-end mt-4">
                        <button @click="cerrarModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista de Tarjetas -->
        <div v-if="!isTableView && paginatedTickets.length > 0"
            class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
            <div v-for="ticket in paginatedTickets" :key="ticket.id" class="relative w-full z-10">
                <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg" :class="{
                    'bg-orange-600': ticket.tic_estado === 'Abierto',
                    'bg-gray-600': ticket.tic_estado === 'Asignado',
                    'bg-blue-600': ticket.tic_estado === 'En progreso',
                    'bg-green-600': ticket.tic_estado === 'Resuelto',
                    'bg-red-600': ticket.tic_estado === 'Cerrado',
                    'bg-yellow-600': ticket.tic_estado === 'Reabierto',
                }"></span>
                <div class="relative h-full p-4 bg-white border-2 rounded-lg shadow-lg transition-shadow duration-300 hover:shadow-xl z-10"
                    :class="{
                        'border-orange-600': ticket.tic_estado === 'Abierto',
                        'border-gray-600': ticket.tic_estado === 'Asignado',
                        'border-blue-600': ticket.tic_estado === 'En progreso',
                        'border-green-600': ticket.tic_estado === 'Resuelto',
                        'border-red-600': ticket.tic_estado === 'Cerrado',
                        'border-yellow-600': ticket.tic_estado === 'Reabierto',
                    }">
                    <div class="absolute px-3 py-1 text-xs font-semibold transition-colors duration-300 rounded-full shadow-md top-2 right-2"
                        :class="{
                            'bg-orange-600 text-white': ticket.tic_estado === 'Abierto',
                            'bg-gray-600 text-white': ticket.tic_estado === 'Asignado',
                            'bg-blue-600 text-white': ticket.tic_estado === 'En progreso',
                            'bg-green-600 text-white': ticket.tic_estado === 'Resuelto',
                            'bg-red-600 text-white': ticket.tic_estado === 'Cerrado',
                            'bg-yellow-600 text-white': ticket.tic_estado === 'Reabierto',
                        }">
                        {{ ticket.tic_estado }}
                    </div>

                    <div class="mt-4">
                        <h2 class="text-lg font-bold text-gray-800 uppercase truncate">
                            {{ ticket.tic_titulo }}
                        </h2>
                        <hr class="my-2 border-gray-200" />
                    </div>

                    <div class="flex flex-col space-y-2 text-sm text-gray-600">
                        <span><strong>Prioridad:</strong> {{ ticket.prioridad }}</span>
                        <span><strong>Categoría:</strong> {{ ticket.categoria }}</span>
                        <span><strong>Creado el:</strong> {{ new Date(ticket.created_at).toLocaleDateString() }}</span>
                    </div>

                    <!-- Acciones -->
                    <div class="flex justify-end mt-auto space-x-3">
                        <button v-if="ticket.tic_estado === 'Asignado' || ticket.tic_estado === 'Reabierto'"
                            @click="aceptarTicket(ticket)"
                            class="text-blue-500 hover:text-blue-700 transition-colors duration-200">
                            <i class="fas fa-check mr-1"></i> Aceptar
                        </button>

                        <!-- Botón de finalizar -->
                        <button v-if="ticket.tic_estado === 'En progreso'" @click="finalizarTicket(ticket)"
                            class="text-green-500 hover:text-green-700 transition-colors duration-200">
                            <i class="fas fa-check-circle mr-1"></i> Finalizar
                        </button>

                        <!-- Botón para ver detalles -->
                        <button @click="verDetalles(ticket)"
                            class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                            <i class="mr-1 fas fa-eye"></i> Ver
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista de Tabla -->
        <div v-if="isTableView && paginatedTickets.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            N°
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            Categoría
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            Título
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            Prioridad
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            Estado
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-for="(ticket, index) in paginatedTickets" :key="ticket.id"
                        class="transition-colors duration-200 border-b hover:bg-gray-100">
                        <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            {{ index + 1 }}
                        </td>
                        <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            {{ ticket.categoria }}
                        </td>
                        <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            {{ ticket.tic_titulo }}
                        </td>
                        <td class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            {{ ticket.prioridad }}
                        </td>
                        <td class="px-2 py-2 text-xs sm:px-4 sm:py-3 sm:text-sm md:text-base">
                            <span :class="[ 'px-2 py-1 text-xs font-semibold rounded-full sm:text-xs md:text-sm', getEstadoLabelClass(ticket.tic_estado), ]">
                                {{ ticket.tic_estado }}
                            </span>
                        </td>
                        <td class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0">
                            <button v-if="ticket.tic_estado === 'Asignado'" @click="aceptarTicket(ticket)"
                                class="text-blue-500 hover:text-blue-700 transition-colors duration-200">
                                <i class="fas fa-check mr-1"></i> Aceptar
                            </button>
                            <button v-if="ticket.tic_estado === 'En progreso'" @click="finalizarTicket(ticket)"
                                class="text-green-500 hover:text-green-700 transition-colors duration-200">
                                <i class="fas fa-check-circle mr-1"></i> Finalizar
                            </button>
                            <button @click="verDetalles(ticket)"
                                class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                <i class="fas fa-eye mr-1"></i> Ver
                            </button>
                        </td>
                    </tr>
                    <tr v-if="paginatedTickets.length === 0">
                        <td colspan="7" class="px-4 py-3 text-xs text-center text-gray-500 sm:text-sm">
                            No se encontraron resultados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="flex justify-center items-center mt-6">
            <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                :class="{'bg-green-500 text-white': currentPage === page, 'bg-gray-200 text-gray-600': currentPage !== page}"
                class="mx-1 px-4 py-2 rounded hover:bg-gray-300">
                {{ page }}
            </button>
        </div>

        <!-- Modal de Imagen Ampliada -->
        <div v-if="isImageModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 z-50">
            <div class="relative max-w-4xl p-4 bg-white rounded-lg shadow-lg">
                <img :src="selectedImageUrl" alt="Imagen ampliada" class="max-w-full max-h-screen" />
                <button @click="closeImageModal"
                    class="absolute top-2 right-2 text-white text-2xl font-bold bg-gray-800 rounded-full px-2 focus:outline-none">
                    &times;
                </button>
            </div>
        </div>
    </div>
</template>
