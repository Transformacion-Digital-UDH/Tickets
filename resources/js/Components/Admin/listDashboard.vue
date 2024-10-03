<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const openTickets = ref(0);
const asignTickets = ref(0);
const inProgressTickets = ref(0);
const resultTickets = ref(0);
const closeTickets = ref(0);
const reopenTickets = ref(0);
const todayTickets = ref(0);

const fetchTicketsData = async () => {
    try {
        const response = await axios.get("/dashboard/fetch-tickets-data");
        const data = response.data;

        openTickets.value = data.openTickets;
        asignTickets.value = data.asignTickets;
        inProgressTickets.value = data.inProgressTickets;
        resultTickets.value = data.resultTickets;
        closeTickets.value = data.closeTickets;
        reopenTickets.value = data.reopenTickets;
        todayTickets.value = data.todayTickets;
    } catch (error) {
        console.error("Error fetching tickets data:", error);
    }
};

onMounted(() => {
    fetchTicketsData();
});
</script>

<template>
    <div
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 p-3"
    >
        <div class="card">
            <div class="icon-container">
                <i class="fas fa-hourglass-half icon"></i>
            </div>
            <div class="content">
                <h3>Abiertos</h3>
                <p class="number">{{ openTickets }}</p>
                <p class="status">{{ openTickets }} tickets abiertos</p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-hourglass-half icon"></i>
            </div>
            <div class="content">
                <h3>Asignados</h3>
                <p class="number">{{ asignTickets }}</p>
                <p class="status">{{ asignTickets }} tickets asignados</p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-spinner icon"></i>
            </div>
            <div class="content">
                <h3>En Progreso</h3>
                <p class="number">{{ inProgressTickets }}</p>
                <p class="status">
                    {{ inProgressTickets }} tickets en progreso
                </p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-check icon"></i>
            </div>
            <div class="content">
                <h3>Resueltos</h3>
                <p class="number">{{ resultTickets }}</p>
                <p class="status">{{ resultTickets }} tickets resueltos</p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-check icon"></i>
            </div>
            <div class="content">
                <h3>Cerrados</h3>
                <p class="number">{{ closeTickets }}</p>
                <p class="status">{{ closeTickets }} tickets cerrados</p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-check icon"></i>
            </div>
            <div class="content">
                <h3>Reabiertos</h3>
                <p class="number">{{ reopenTickets }}</p>
                <p class="status">{{ reopenTickets }} tickets cerrados</p>
            </div>
        </div>

        <div class="card">
            <div class="icon-container">
                <i class="fas fa-calendar-day icon"></i>
            </div>
            <div class="content">
                <h3>Hoy</h3>
                <p class="number">{{ todayTickets }}</p>
                <p class="status">{{ todayTickets }} tickets creados hoy</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    background-color: #757776;
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.icon-container {
    background-color: #e1f5fe;
    border-radius: 50%;
    padding: 15px;
    margin-right: 15px;
}

.icon {
    color: #7d5fff;
    font-size: 30px;
}

.content {
    text-align: left;
}

h3 {
    font-size: 18px;
    margin: 0;
    color: #fff;
}

.number {
    font-size: 28px;
    color: #fff;
    margin: 0;
}

.status {
    font-size: 14px;
    color: #d1ffd7;
    margin-top: 5px;
}

@media (max-width: 768px) {
    h3 {
        font-size: 16px;
    }
    .number {
        font-size: 24px;
    }
    .status {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .card {
        padding: 12px;
        align-items: center;
        text-align: center;
    }
    .icon-container {
        margin-bottom: 10px;
    }
    .number {
        font-size: 20px;
    }
    .status {
        font-size: 12px;
    }
}
</style>
