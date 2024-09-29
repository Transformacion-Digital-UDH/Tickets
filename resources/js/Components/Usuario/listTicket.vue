<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const activeTab = ref("open");
const tickets = ref({
    open: [],
    inProgress: [],
    closed: [],
});

const loadTickets = async () => {
    try {
        const response = await axios.get("/user-tickets");
        const allTickets = response.data;

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

onMounted(loadTickets);

const showTickets = (status) => {
    activeTab.value = status;
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
                                    class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="$emit('view', item)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                                        title="Ver detalles"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button
                                        @click="$emit('edit', item)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                                        title="Editar"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button
                                        @click="$emit('eliminar', item)"
                                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                                        title="Cancelar"
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
                                    class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="$emit('view', item)"
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
                                    class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                                >
                                    <button
                                        @click="$emit('view', item)"
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
        </div>
    </div>
</template>

<style scoped>
.shadow-custom {
    box-shadow: 0 5px 8px rgba(0, 0, 0, 0.5);
}
</style>
