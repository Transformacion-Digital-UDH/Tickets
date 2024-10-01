<template>
    <div>
      <h1>Tickets Asignados</h1>
  
      <!-- Mostrar mensaje de éxito si existe -->
      <div v-if="success" class="alert alert-success">
        {{ success }}
      </div>
  
      <!-- Mostrar lista de tickets asignados al soporte -->
      <div v-if="tickets.length > 0" class="row">
        <div class="col-md-4 mb-3" v-for="ticket in tickets" :key="ticket.id">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">{{ ticket.tic_titulo }}</h5>
              <p class="card-text">{{ ticket.tic_descripcion }}</p>
              <p class="card-text">
                <strong>Estado:</strong> {{ ticket.tic_estado }}
              </p>
              <button 
                v-if="ticket.tic_estado !== 'Finalizado'"
                @click="finalizarTicket(ticket.id)"
                class="btn btn-primary">
                Finalizar Ticket
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Mostrar mensaje si no hay tickets -->
      <div v-else>
        <p>No hay tickets asignados.</p>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted } from 'vue';
  
  export default {
    props: {
      success: String,
    },
    setup() {
      const tickets = ref([]);
  
      // Obtener los tickets asignados al soporte al montar el componente
      onMounted(async () => {
        try {
          const response = await fetch('support-optener');
          const data = await response.json();
  
          console.log(data); // Verificar si los datos se están recibiendo
          tickets.value = data; // Asignar los tickets a la variable
        } catch (error) {
          console.error('Error al obtener los tickets:', error);
        }
      });
  
      // Método para finalizar un ticket
      const finalizarTicket = async (id) => {
        try {
          const response = await fetch(`support-finalizar`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
          });
  
          const data = await response.json();
          if (data.status) {
            alert(data.msg);
  
            // Actualizar el estado del ticket a "Finalizado" en la lista
            const ticket = tickets.value.find(ticket => ticket.id === id);
            if (ticket) ticket.tic_estado = 'Finalizado';
          }
        } catch (error) {
          console.error('Error al finalizar el ticket:', error);
        }
      };
  
      return { tickets, finalizarTicket };
    },
  };
  </script>
  
  <style>
  .card {
    border: 1px solid #e0e0e0; /* Añade un borde a la tarjeta */
    border-radius: 8px; /* Bordes redondeados */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra sutil */
  }
  
  .card-body {
    padding: 16px; /* Espaciado interno */
  }
  
  .row {
    display: flex; /* Disposición en flex */
    flex-wrap: wrap; /* Permitir que las tarjetas se envuelvan en múltiples filas */
  }
  
  .col-md-4 {
    flex: 1 0 30%; /* Asegura que las tarjetas ocupen un tercio del ancho en pantallas medianas */
    max-width: 30%; /* Limitar el ancho máximo */
  }
  </style>
  