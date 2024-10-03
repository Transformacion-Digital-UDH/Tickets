<template>
  <div class="p-4">
    <h1 class="text-xl font-bold text-center mb-4">Tickets Asignados</h1>

    <!-- Botón para cambiar la vista -->
    <div class="text-right mb-4">
      <button @click="toggleView"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-300">
        {{ isTableView ? 'Vista de Tarjetas' : 'Vista de Tabla' }}
      </button>
    </div>

    <!-- Mostrar mensaje de éxito si existe -->
    <div v-if="success" class="alert alert-success mb-4 text-center text-green-600">
      {{ success }}
    </div>

    <!-- Vista de Tarjetas -->
    <div v-if="!isTableView && tickets.length > 0"
      class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
      <div v-for="ticket in tickets" :key="ticket.id" class="relative w-full">
        <span class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg" :class="{
          'bg-orange-600': ticket.tic_estado === 'Abierto',
          'bg-gray-600': ticket.tic_estado === 'Asignado',
          'bg-blue-600': ticket.tic_estado === 'En progreso',
          'bg-green-600': ticket.tic_estado === 'Resuelto',
          'bg-red-600': ticket.tic_estado === 'Cerrado',
          'bg-yellow-600': ticket.tic_estado === 'Reabierto',
        }"></span>
        <div
          class="relative h-full p-4 bg-white border-2 rounded-lg shadow-lg transition-shadow duration-300 hover:shadow-xl"
          :class="{
            'border-orange-600': ticket.tic_estado === 'Abierto',
            'border-gray-600': ticket.tic_estado === 'Asignado',
            'border-blue-600': ticket.tic_estado === 'En progreso',
            'border-green-600': ticket.tic_estado === 'Resuelto',
            'border-red-600': ticket.tic_estado === 'Cerrado',
            'border-yellow-600': ticket.tic_estado === 'Reabierto',
          }">
          <div
            class="absolute px-3 py-1 text-xs font-semibold transition-colors duration-300 rounded-full shadow-md top-2 right-2"
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
            <button v-if="ticket.tic_estado === 'Asignado'" @click="aceptarTicket(ticket)"
              class="text-blue-500 hover:text-blue-700 transition-colors duration-200">
              <i class="fas fa-check mr-1"></i> Aceptar
            </button>

            <!-- Botón para ver detalles -->
            <button @click="verDetalles(ticket)" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
              <i class="mr-1 fas fa-eye"></i> Ver
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalles Embebido Directamente -->
    <div v-if="mostrarModal" class="fixed inset-0 flex items-center justify-center bg-gray-400 bg-opacity-30">
      <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg">
        <div class="p-4 border-2 border-gray-400 rounded-lg">
          <h2 class="mb-4 text-xl font-bold text-gray-600">
            Detalles del Ticket
          </h2>
          <table class="w-full border-collapse">
            <thead>
              <tr>
                <th class="py-2 pr-10 text-left text-gray-600 border-b-2 border-gray-400">Campo</th>
                <th class="py-2 text-left text-gray-600 border-b-2 border-gray-400">Valor</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(field, index) in formFieldsVer" :key="index" class="border-b border-gray-400">
                <td class="py-2 pr-10 font-semibold text-left text-gray-500">{{ field.label }}</td>
                <td class="py-2 text-left text-gray-600">{{ ticketSeleccionado[field.name] || "No disponible" }}</td>
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
    const isTableView = ref(false);
    const mostrarModal = ref(false);
    const ticketSeleccionado = ref(null);

    const formFieldsVer = ref([
      { label: 'Título', name: 'tic_titulo' },
      { label: 'Descripción', name: 'tic_descripcion' },
      { label: 'Prioridad', name: 'prioridad' },
      { label: 'Categoría', name: 'categoria' },
      { label: 'Estado', name: 'tic_estado' },
      { label: 'Fecha de Creación', name: 'created_at' },
    ]);

    const toggleView = () => {
      isTableView.value = !isTableView.value;
    };

    onMounted(async () => {
      try {
        const response = await fetch('support-optener');
        const data = await response.json();
        tickets.value = data.map((ticket) => ({
          id: ticket.id,
          tic_titulo: ticket.tic_titulo,
          tic_descripcion: ticket.tic_descripcion,
          tic_estado: ticket.tic_estado,
          prioridad: ticket.prioridad ? ticket.prioridad.pri_nombre : 'N/A',
          categoria: ticket.categoria ? ticket.categoria.cat_nombre : 'N/A',
          created_at: ticket.created_at,
        }));
      } catch (error) {
        console.error('Error al obtener los tickets:', error);
      }
    });

    const verDetalles = (ticket) => {
      ticketSeleccionado.value = { ...ticket };  // Asignar el ticket seleccionado
      mostrarModal.value = true;  // Mostrar el modal
    };

    const cerrarModal = () => {
      mostrarModal.value = false;  // Cerrar el modal
    };

    const aceptarTicket = async (ticket) => {
      try {
        const response = await fetch(`/soporte/tickets/aceptar/${ticket.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });

        if (!response.ok) {
          throw new Error('Error al aceptar el ticket');
        }

        const data = await response.json();
        if (data.status) {
          const index = tickets.value.findIndex((t) => t.id === ticket.id);
          if (index !== -1) {
            tickets.value[index] = data.ticket;
          }
        } else {
          console.error(data.msg);
        }
      } catch (error) {
        console.error('Error al aceptar el ticket:', error);
      }
    };

    return { tickets, isTableView, toggleView, verDetalles, cerrarModal, mostrarModal, ticketSeleccionado, formFieldsVer, aceptarTicket };
  },
};
</script>
