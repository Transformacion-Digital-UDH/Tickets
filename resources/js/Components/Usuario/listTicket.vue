<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const activeTab = ref("");
const tickets = ref({
    open: [],
    inProgress: [],
    closed: [],
});

const loadTickets = async () => {
    try {
        const response = await axios.get('/user-tickets');
        const allTickets = response.data;

        tickets.value.open = allTickets.filter(ticket => ticket.tic_estado === 'Abierto');
        tickets.value.inProgress = allTickets.filter(ticket => ticket.tic_estado === 'En progreso');
        tickets.value.closed = allTickets.filter(ticket => ticket.tic_estado === 'Cerrado');
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
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Mis Tickets
        </h1>
        <div class="overflow-x-auto bg-[#2EBAA1] rounded-lg shadow-md p-6">
            <div class="overflow-x-auto bg-white rounded-lg shadow-md p-4 flex mb-3 justify-center">
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-orange-600 rounded-lg shadow-md hover:bg-orange-800"
                    @click="showTickets('open')"
                >
                    Mis Tickets Recientes
                </button>
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-blue-600 rounded-lg shadow-md hover:bg-blue-800"
                    @click="showTickets('in-progress')"
                >
                    Mis Tickets En progreso
                </button>
                <button
                    class="mr-5 w-full sm:w-auto flex justify-center items-center px-9 py-2.5 text-xs sm:text-sm font-semibold text-white transition-all duration-300 bg-red-600 rounded-lg shadow-md hover:bg-red-800"
                    @click="showTickets('closed')"
                >
                    Mis Tickets Cerrados
                </button>
            </div>
            <div v-if="activeTab === 'open'">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">N°</th>
                            <th class="py-2 px-4 border-b">Título</th>
                            <th class="py-2 px-4 border-b">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ticket, index) in tickets.open" :key="ticket.id">
                            <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_titulo }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_descripcion }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="activeTab === 'in-progress'">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">N°</th>
                            <th class="py-2 px-4 border-b">Título</th>
                            <th class="py-2 px-4 border-b">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ticket, index) in tickets.inProgress" :key="ticket.id">
                            <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_titulo }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_descripcion }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="activeTab === 'closed'">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">N°</th>
                            <th class="py-2 px-4 border-b">Título</th>
                            <th class="py-2 px-4 border-b">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(ticket, index) in tickets.closed" :key="ticket.id">
                            <td class="py-2 px-4 border-b">{{ index + 1 }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_titulo }}</td>
                            <td class="py-2 px-4 border-b">{{ ticket.tic_descripcion }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>