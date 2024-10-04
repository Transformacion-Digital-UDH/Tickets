<template>
  <div class="grid grid-cols-1 gap-4 p-4 dashboard-container md:grid-cols-2 lg:grid-cols-3">
    <!-- Tarjeta Asignados -->
    <div class="card">
      <div class="icon-container">
        <i class="fas fa-ticket-alt icon"></i>
      </div>
      <div class="content">
        <h3>Asignados</h3>
        <p class="number">{{ assignedTickets }}</p>
        <p class="status">+10 desde la última actualización</p>
      </div>
    </div>

    <!-- Tarjeta Finalizados -->
    <div class="card">
      <div class="icon-container">
        <i class="fas fa-check icon"></i>
      </div>
      <div class="content">
        <h3>Resueltos</h3>
        <p class="number">{{ finalizedTickets }}</p>
        <p class="status">+5 desde la última actualización</p>
      </div>
    </div>

    <!-- Tarjeta En Progreso -->
    <div class="card">
      <div class="icon-container">
        <i class="fas fa-spinner icon"></i>
      </div>
      <div class="content">
        <h3>En Progreso</h3>
        <p class="number">{{ inProgressTickets }}</p>
        <p class="status">+3 desde la última actualización</p>
      </div>
    </div>

    <!-- Tarjeta Cerrados -->
    <div class="card">
      <div class="icon-container">
        <i class="fas fa-times-circle icon"></i>
      </div>
      <div class="content">
        <h3>Cerrados</h3>
        <p class="number">{{ closedTickets }}</p>
        <p class="status">+2 desde la última actualización</p>
      </div>
    </div>

    <!-- Tarjeta Hoy -->
    <div class="card">
      <div class="icon-container">
        <i class="fas fa-calendar-day icon"></i>
      </div>
      <div class="content">
        <h3>Hoy</h3>
        <p class="number">{{ todayTickets }}</p>
        <p class="status">+1 desde la última actualización</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
  setup() {
    // Estados reactivos para los contadores de las tarjetas
    const assignedTickets = ref(0);
    const finalizedTickets = ref(0);
    const inProgressTickets = ref(0);
    const closedTickets = ref(0);
    const todayTickets = ref(0);

    // Función para cargar los tickets y filtrarlos por estado
    const cargarDatosDashboard = async () => {
      try {
        // Reutilizando la misma API para obtener los tickets
        const response = await fetch('support-optener');
        const data = await response.json();

        // Obtener la fecha de hoy (sin la hora)
        const hoy = new Date();
        const inicioHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate()).getTime();
        const finHoy = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate() + 1).getTime();

        // Filtrar tickets según su estado
        assignedTickets.value = data.filter(ticket => ticket.tic_estado === 'Asignado').length;
        finalizedTickets.value = data.filter(ticket => ticket.tic_estado === 'Resuelto').length;
        inProgressTickets.value = data.filter(ticket => ticket.tic_estado === 'En progreso').length;
        closedTickets.value = data.filter(ticket => ticket.tic_estado === 'Cerrado').length;

        // Filtrar los tickets creados hoy (comparamos la fecha sin la hora)
        todayTickets.value = data.filter(ticket => {
          const fechaCreacion = new Date(ticket.created_at).getTime();
          return fechaCreacion >= inicioHoy && fechaCreacion < finHoy;
        }).length;
      } catch (error) {
        console.error('Error al cargar los datos del dashboard:', error);
      }
    };

    // Llamada para cargar los datos cuando el componente se monta
    onMounted(cargarDatosDashboard);

    return {
      assignedTickets,
      finalizedTickets,
      inProgressTickets,
      closedTickets,
      todayTickets,
    };
  },
};
</script>

<style scoped>
.dashboard-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

/* Nueva tarjeta de estilo unificado */
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
</style>
