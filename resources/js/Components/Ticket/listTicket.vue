<script>
import axios from "axios";
import { ref, reactive, onMounted } from "vue";
export default {
    data() {
        return {
            currentPage: 1,
            ticketsPerPage: 7,

            isCardView: true,
            mostrarModalCrearTicket: false,
            mostrarModalDetalles: false,
            mostrarModalAsignarSoporte: false,
            ticketSeleccionado: {},

            nuevoTicket: {
                titulo: "",
                descripcion: "",
                prioridad: "", // Aquí se almacena el ID de la prioridad seleccionada
                usuario: "",
                categoria: "",
                pabellon: "",
            },

            soporteAsignado: "",
            searchQuery: "", // Barra de búsqueda
            tickets: [],
            usuarios: [],
            prioridades: [],
            categorias: [],
            pabellones: [],
        };
    },
    computed: {
        totalPages() {
            return Math.ceil(this.filteredTickets.length / this.ticketsPerPage);
        },
        paginatedTickets() {
            const start = (this.currentPage - 1) * this.ticketsPerPage;
            const end = this.currentPage * this.ticketsPerPage;
            return this.filteredTickets.slice(start, end);
        },
        filteredTickets() {
            return this.tickets.filter((ticket) => {
                const search = this.searchQuery.toLowerCase();
                return (
                    ticket.tic_titulo.toLowerCase().includes(search) ||
                    (ticket.tic_descripcion &&
                        ticket.tic_descripcion
                            .toLowerCase()
                            .includes(search)) ||
                    (ticket.tic_estado &&
                        ticket.tic_estado.toLowerCase().includes(search)) ||
                    (ticket.prioridad &&
                        ticket.prioridad.pri_nombre
                            .toLowerCase()
                            .includes(search)) ||
                    (ticket.user &&
                        ticket.user.name.toLowerCase().includes(search)) ||
                    (ticket.categoria &&
                        ticket.categoria.cat_nombre
                            .toLowerCase()
                            .includes(search))
                );
            });
        },
    },
    created() {
        this.fetchTickets();
        this.fetchPrioridades();
        this.fetchUsuarios();
        this.fetchCategorias();
        this.fetchPabellones();
    },

    methods: {
        changePage(page) {
            this.currentPage = page;
        },
        toggleView() {
            this.isCardView = !this.isCardView;
        },
        async fetchTickets() {
            try {
                const response = await axios.get("/tickets");
                this.tickets = response.data;
            } catch (error) {
                console.error("Error al traer los tickets:", error);
            }
        },
        async fetchPrioridades() {
            try {
                const response = await axios.get("/prioridades");
                this.prioridades = response.data.map((prioridad) => ({
                    id: prioridad.id,
                    nombre: prioridad.pri_nombre,
                }));
            } catch (error) {
                console.error("Error al traer las prioridades:", error);
            }
        },
        async fetchUsuarios() {
            try {
                const response = await axios.get("/usuarios");
                this.usuarios = response.data.map((usuario) => ({
                    id: usuario.id,
                    nombre: usuario.name,
                }));
            } catch (error) {
                console.error("Error al traer los usuarios:", error);
            }
        },
        async fetchCategorias() {
            try {
                const response = await axios.get("/categorias");
                this.categorias = response.data.map((categoria) => ({
                    id: categoria.id,
                    nombre: categoria.cat_nombre,
                }));
            } catch (error) {
                console.error("Error al traer las categorías:", error);
            }
        },
        async fetchPabellones() {
            try {
                const response = await axios.get("/pabellones");
                this.pabellones = response.data.map((pabellon) => ({
                    id: pabellon.id,
                    nombre: pabellon.pab_nombre,
                }));
            } catch (error) {
                console.error("Error al traer los pabellones:", error);
            }
        },

        async crearTicket() {
            try {
                await axios.post("/tickets", {
                    tic_titulo: this.nuevoTicket.titulo,
                    tic_descripcion: this.nuevoTicket.descripcion,
                    pri_id: this.nuevoTicket.prioridad,
                    use_id: this.nuevoTicket.usuario,
                    cat_id: this.nuevoTicket.categoria,
                    pab_id: this.nuevoTicket.pabellon,
                    tic_estado: "Abierto",
                    tic_activo: true,
                });

                // Recargar la lista de tickets
                await this.fetchTickets(); // Vuelve a cargar los tickets desde la API

                // Reinicializar el formulario del modal
                this.nuevoTicket = {
                    titulo: "",
                    descripcion: "",
                    prioridad: "",
                    usuario: "",
                    categoria: "",
                    pabellon: "",
                };

                // Cerrar el modal de creación
                this.cerrarCrearTicketModal();
            } catch (error) {
                console.error("Error al crear el ticket:", error);
            }
        },
        showCrearTicketModal() {
            this.mostrarModalCrearTicket = true;
        },
        cerrarCrearTicketModal() {
            this.mostrarModalCrearTicket = false;
        },
        showDetallesModal(ticket) {
            this.ticketSeleccionado = ticket;
            this.mostrarModalDetalles = true;
        },
        cerrarDetallesModal() {
            this.mostrarModalDetalles = false;
        },
        showAsignarSoporteModal(ticket) {
            this.ticketSeleccionado = ticket;
            this.mostrarModalAsignarSoporte = true;
        },
        cerrarAsignarSoporteModal() {
            this.mostrarModalAsignarSoporte = false;
        },
        asignarSoporte() {
            alert(
                `El ticket "${this.ticketSeleccionado.tic_titulo}" ha sido asignado a ${this.soporteAsignado}`
            );
            this.cerrarAsignarSoporteModal();
        },
    },
};
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-2xl font-bold">Lista de Tickets</h1>

        <!-- Barra de búsqueda -->
        <div class="mb-4">
            <input
                v-model="searchQuery"
                type="text"
                placeholder="Buscar por título, estado, prioridad, usuario o categoría..."
                class="w-full p-2 border rounded"
            />
        </div>
        <!--Boton para crear un ticket y vista tabla or card-->
        <div class="flex justify-end mb-4">
            <button
                @click="showCrearTicketModal"
                class="px-4 py-2 mr-2 text-white transition duration-300 bg-green-500 rounded hover:bg-green-600"
            >
                Crear Ticket
            </button>
            <button
                @click="toggleView"
                class="px-4 py-2 text-white transition duration-300 bg-blue-500 rounded hover:bg-blue-600"
            >
                <i :class="isCardView ? 'fas fa-list' : 'fas fa-table'"></i>
            </button>
        </div>

        <!-- Vista en tarjetas -->
        <div
            v-if="isCardView"
            class="container relative flex flex-col justify-between h-full px-4 mx-auto mt-5 max-w-9xl xl:px-0"
        >
            <div
                class="grid w-full grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
            >
                <div
                    v-for="ticket in filteredTickets"
                    :key="ticket.id"
                    class="relative w-full"
                >
                    <!-- Fondo coloreado detrás de la tarjeta -->
                    <span
                        class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg"
                        :class="{
                            'bg-orange-600': ticket.tic_estado === 'Abierto',
                            'bg-blue-600': ticket.tic_estado === 'En progreso',
                            'bg-green-600': ticket.tic_estado === 'Finalizado',
                            'bg-red-600': ticket.tic_estado === 'Cerrado',
                        }"
                    ></span>

                    <!-- Contenido de la tarjeta -->
                    <div
                        class="relative h-full p-6 transition-transform duration-300 ease-in-out transform bg-white border-2 rounded-lg hover:scale-105 hover:shadow-2xl"
                        :class="{
                            'border-orange-600':
                                ticket.tic_estado === 'Abierto',
                            'border-blue-600':
                                ticket.tic_estado === 'En progreso',
                            'border-green-600':
                                ticket.tic_estado === 'Finalizado',
                            'border-red-600': ticket.tic_estado === 'Cerrado',
                        }"
                    >
                        <!-- Estado del ticket como badge -->
                        <div
                            class="absolute px-3 py-1 text-xs font-semibold transition-colors duration-300 rounded-full shadow-md top-3 right-3"
                            :class="{
                                'bg-orange-600 text-white':
                                    ticket.tic_estado === 'Abierto',
                                'bg-blue-600 text-white':
                                    ticket.tic_estado === 'En progreso',
                                'bg-green-600 text-white':
                                    ticket.tic_estado === 'Finalizado',
                                'bg-red-600 text-white':
                                    ticket.tic_estado === 'Cerrado',
                            }"
                        >
                            {{ ticket.tic_estado }}
                        </div>

                        <!-- Título del ticket -->
                        <div>
                            <h2
                                class="text-lg font-bold text-gray-800 uppercase truncate"
                            >
                                {{ ticket.tic_titulo }}
                            </h2>
                            <hr class="my-2 border-gray-200" />
                        </div>

                        <!-- Información detallada del ticket -->
                        <div
                            class="flex flex-col space-y-2 text-sm text-gray-600"
                        >
                            <span
                                ><strong>Prioridad:</strong>
                                {{
                                    ticket.prioridad
                                        ? ticket.prioridad.pri_nombre
                                        : "N/A"
                                }}</span
                            >
                            <span
                                ><strong>Usuario:</strong>
                                {{
                                    ticket.user ? ticket.user.name : "N/A"
                                }}</span
                            >
                            <span
                                ><strong>Categoría:</strong>
                                {{
                                    ticket.categoria
                                        ? ticket.categoria.cat_nombre
                                        : "N/A"
                                }}</span
                            >
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end mt-auto space-x-3">
                            <button
                                @click="showDetallesModal(ticket)"
                                class="flex items-center text-blue-500 transition duration-300 hover:text-blue-700"
                            >
                                <i class="mr-1 fas fa-eye"></i> Ver
                            </button>
                            <button
                                @click="showAsignarSoporteModal(ticket)"
                                class="flex items-center text-purple-500 transition duration-300 hover:text-purple-700"
                            >
                                <i class="mr-1 fas fa-user-plus"></i> Asignar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista en tabla -->
        <div v-else class="flex flex-col min-h-[603px]">
            <!-- Añadí min-h-[500px] para asegurar el espacio mínimo -->
            <div class="flex-grow overflow-x-auto">
                <table
                    class="min-w-full bg-white border-collapse rounded-lg shadow-lg"
                >
                    <thead
                        class="text-sm leading-normal text-gray-600 uppercase bg-gray-200"
                    >
                        <tr>
                            <th class="px-6 py-3 border-b">Título</th>
                            <th class="px-6 py-3 border-b">Descripción</th>
                            <th class="px-6 py-3 border-b">Prioridad</th>
                            <th class="px-6 py-3 border-b">Estado</th>
                            <th class="px-6 py-3 border-b">Usuario</th>
                            <th class="px-6 py-3 border-b">Categoría</th>
                            <th class="px-6 py-3 border-b">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        <tr
                            v-for="ticket in paginatedTickets"
                            :key="ticket.id"
                            class="transition duration-300 ease-in-out border-b hover:bg-gray-100"
                        >
                            <td class="px-6 py-4">{{ ticket.tic_titulo }}</td>
                            <td class="px-6 py-4">
                                {{ ticket.tic_descripcion }}
                            </td>
                            <td class="px-6 py-4">
                                {{
                                    ticket.prioridad
                                        ? ticket.prioridad.pri_nombre
                                        : "N/A"
                                }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded-full"
                                    :class="{
                                        'bg-orange-100 text-orange-600':
                                            ticket.tic_estado === 'Abierto',
                                        'bg-blue-100 text-blue-600':
                                            ticket.tic_estado === 'En progreso',
                                        'bg-green-100 text-green-600':
                                            ticket.tic_estado === 'Finalizado',
                                        'bg-red-100 text-red-600':
                                            ticket.tic_estado === 'Cerrado',
                                    }"
                                >
                                    {{ ticket.tic_estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                {{ ticket.user ? ticket.user.name : "N/A" }}
                            </td>
                            <td class="px-6 py-4">
                                {{
                                    ticket.categoria
                                        ? ticket.categoria.cat_nombre
                                        : "N/A"
                                }}
                            </td>
                            <td class="flex justify-center px-6 py-4 space-x-2">
                                <button
                                    @click="showDetallesModal(ticket)"
                                    class="p-2 text-white transition duration-300 ease-in-out bg-blue-500 rounded-full hover:bg-blue-700"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button
                                    @click="showAsignarSoporteModal(ticket)"
                                    class="p-2 text-white transition duration-300 ease-in-out bg-purple-500 rounded-full hover:bg-purple-700"
                                >
                                    <i class="fas fa-user-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación con espacio fijo en la parte inferior -->
            <div class="flex justify-center mt-4 mb-4 space-x-2">
                <!-- Añadí mb-4 para separarlo del final -->
                <button
                    v-for="page in totalPages"
                    :key="page"
                    @click="changePage(page)"
                    :class="{
                        'bg-blue-500 text-white': page === currentPage,
                        'bg-white text-blue-500 border': page !== currentPage,
                    }"
                    class="px-4 py-2 transition duration-300 ease-in-out border rounded-lg hover:bg-blue-500 hover:text-white"
                >
                    {{ page }}
                </button>
            </div>
        </div>

        <!-- Modal Crear Ticket -->
        <div
            v-if="mostrarModalCrearTicket"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="bg-white p-8 rounded-lg shadow-lg max-w-[550px] w-full mx-4"
            >
                <h2 class="mb-6 text-2xl font-bold text-gray-800">
                    Crear Nuevo Ticket
                </h2>

                <!-- Formulario para crear el ticket -->
                <form @submit.prevent="crearTicket">
                    <!-- Campo Título -->
                    <div class="mb-5">
                        <label
                            for="titulo"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Título:</label
                        >
                        <input
                            type="text"
                            v-model="nuevoTicket.titulo"
                            id="titulo"
                            placeholder="Título del ticket"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        />
                    </div>

                    <!-- Campo Descripción -->
                    <div class="mb-5">
                        <label
                            for="descripcion"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Descripción:</label
                        >
                        <textarea
                            v-model="nuevoTicket.descripcion"
                            id="descripcion"
                            placeholder="Descripción del ticket"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        ></textarea>
                    </div>

                    <!-- Prioridad -->
                    <div class="mb-5">
                        <label
                            for="prioridad"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Prioridad:</label
                        >
                        <select
                            v-model="nuevoTicket.prioridad"
                            id="prioridad"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        >
                            <option
                                v-for="prioridad in prioridades"
                                :key="prioridad.id"
                                :value="prioridad.id"
                            >
                                {{ prioridad.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Usuario -->
                    <div class="mb-5">
                        <label
                            for="usuario"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Usuario:</label
                        >
                        <select
                            v-model="nuevoTicket.usuario"
                            id="usuario"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        >
                            <option
                                v-for="usuario in usuarios"
                                :key="usuario.id"
                                :value="usuario.id"
                            >
                                {{ usuario.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Categoría -->
                    <div class="mb-5">
                        <label
                            for="categoria"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Categoría:</label
                        >
                        <select
                            v-model="nuevoTicket.categoria"
                            id="categoria"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        >
                            <option
                                v-for="categoria in categorias"
                                :key="categoria.id"
                                :value="categoria.id"
                            >
                                {{ categoria.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Pabellón -->
                    <div class="mb-5">
                        <label
                            for="pabellon"
                            class="block mb-3 text-base font-medium text-gray-700"
                            >Pabellón:</label
                        >
                        <select
                            v-model="nuevoTicket.pabellon"
                            id="pabellon"
                            class="w-full px-4 py-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md outline-none focus:border-blue-500 focus:shadow-md"
                        >
                            <option
                                v-for="pabellon in pabellones"
                                :key="pabellon.id"
                                :value="pabellon.id"
                            >
                                {{ pabellon.nombre }}
                            </option>
                        </select>
                    </div>

                    <!-- Botones Crear y Cancelar -->
                    <div class="flex justify-end space-x-4">
                        <button
                            type="submit"
                            class="px-4 py-2 text-white transition duration-300 bg-green-500 rounded-md hover:bg-green-600"
                        >
                            Crear
                        </button>
                        <button
                            @click="cerrarCrearTicketModal"
                            type="button"
                            class="px-4 py-2 text-white transition duration-300 bg-red-500 rounded-md hover:bg-red-600"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Detalles Ticket -->
        <div
            v-if="mostrarModalDetalles"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div
                class="w-full max-w-2xl mx-4 overflow-hidden bg-white shadow-lg sm:rounded-lg"
            >
                <!-- Estilo general de la tarjeta modal -->
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Detalles del Ticket
                    </h3>
                    <p class="max-w-2xl mt-1 text-sm text-gray-500">
                        Información detallada del ticket seleccionado.
                    </p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div
                            class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Título
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.tic_titulo }}
                            </dd>
                        </div>
                        <div
                            class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Descripción
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.tic_descripcion }}
                            </dd>
                        </div>
                        <div
                            class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Prioridad
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.prioridad.pri_nombre }}
                            </dd>
                        </div>
                        <div
                            class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Estado
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.tic_estado }}
                            </dd>
                        </div>
                        <div
                            class="px-4 py-5 bg-gray-50 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Usuario
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.user.name }}
                            </dd>
                        </div>
                        <div
                            class="px-4 py-5 bg-white sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Categoría
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ ticketSeleccionado.categoria.cat_nombre }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <!-- Botón de cerrar -->
                <div class="flex justify-end px-4 py-4 sm:px-6 bg-gray-50">
                    <button
                        @click="cerrarDetallesModal"
                        class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600"
                    >
                        Cerrar
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Asignar Soporte -->
        <div
            v-if="mostrarModalAsignarSoporte"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
        >
            <div class="w-full max-w-lg p-6 bg-white rounded-lg shadow-lg">
                <h2 class="mb-4 text-xl font-bold">Asignar a Soporte</h2>
                <p>
                    <strong>Título:</strong> {{ ticketSeleccionado.tic_titulo }}
                </p>
                <label class="block mb-2">Asignar a:</label>
                <input
                    type="text"
                    v-model="soporteAsignado"
                    class="w-full p-2 mb-4 border rounded"
                />
                <button
                    @click="asignarSoporte"
                    class="px-4 py-2 text-white bg-purple-500 rounded hover:bg-purple-600"
                >
                    Asignar
                </button>
                <button
                    @click="cerrarAsignarSoporteModal"
                    class="px-4 py-2 ml-4 text-white bg-red-500 rounded hover:bg-red-600"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</template>
