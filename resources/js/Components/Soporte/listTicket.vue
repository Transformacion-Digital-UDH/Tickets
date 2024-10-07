<template>
  <div class="p-4">
    <h1 class="text-xl font-bold text-center mb-4">Tickets Asignados</h1>

    <!-- Barra de búsqueda -->
    <div class="relative w-full mb-4">
      <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <i class="text-gray-400 fas fa-search"></i>
      </span>
      <input
        type="text"
        v-model="buscarQuery"
        placeholder="Buscar..."
        class="w-full py-2 placeholder-gray-400 border border-gray-300 rounded-md px-9 focus:border-gray-400 focus:ring focus:ring-gray-400 focus:ring-opacity-5"
      />
    </div>

    <!-- Botón para cambiar la vista (ahora con íconos) -->
    <div class="text-right mb-4">
      <button @click="toggleView" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-300">
        <i :class="isTableView ? 'fas fa-th-large' : 'fas fa-table'"></i>
      </button>
    </div>

    <!-- Mostrar mensaje de éxito si existe -->
    <div v-if="success" class="alert alert-success mb-4 text-center text-green-600">
      {{ success }}
    </div>

    <!-- Vista de Tarjetas -->
    <div v-if="!isTableView && filtrarTickets.length > 0"
      class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
      <div v-for="ticket in filtrarTickets" :key="ticket.id" class="relative w-full">
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
    <div v-if="isTableView && filtrarTickets.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-white">
          <tr>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              N°
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Categoría
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Título
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Prioridad
            </th>
            <th class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Estado
            </th>
            <th class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base">
              Acciones
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="(ticket, index) in filtrarTickets" :key="ticket.id" class="transition-colors duration-200 border-b hover:bg-gray-100">
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
              <span :class="[ 
                  'px-2 py-1 text-xs font-semibold rounded-full sm:text-xs md:text-sm',
                  getEstadoLabelClass(ticket.tic_estado)
                ]">
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
          <tr v-if="filtrarTickets.length === 0">
            <td colspan="7" class="px-4 py-3 text-xs text-center text-gray-500 sm:text-sm">
              No se encontraron resultados.
            </td>
          </tr>
        </tbody>
      </table>
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
                <td class="py-2 text-left text-gray-600">
                  <!-- Mostrar la fecha con formato de hora, minutos y segundos -->
                  <span v-if="field.name === 'created_at'">
                    {{ new Date(ticketSeleccionado.created_at).toLocaleString('es-ES', { 
                        year: 'numeric', month: '2-digit', day: '2-digit', 
                        hour: '2-digit', minute: '2-digit', second: '2-digit' 
                    }) }}
                  </span>
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
  </div>
</template>

<script>
import { ref, onMounted, computed, nextTick } from 'vue';

export default {
  props: {
    success: String,
  },
  setup() {
    const tickets = ref([]);
    const isTableView = ref(false);
    const mostrarModal = ref(false);
    const ticketSeleccionado = ref(null);
    const buscarQuery = ref(""); // Agregamos la propiedad buscarQuery para la barra de búsqueda

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

    const filtrarTickets = computed(() => {
      let filteredTickets = tickets.value;

      if (buscarQuery.value) {
        const query = buscarQuery.value.toLowerCase();
        filteredTickets = filteredTickets.filter(ticket => {
          return (
            (ticket.tic_titulo && ticket.tic_titulo.toLowerCase().includes(query)) ||
            (ticket.prioridad && ticket.prioridad.toLowerCase().includes(query)) ||
            (ticket.categoria && ticket.categoria.toLowerCase().includes(query)) ||
            (ticket.tic_estado && ticket.tic_estado.toLowerCase().includes(query))
          );
        });
      }

      return filteredTickets;
    });

    const recargarTickets = async () => {
      try {
        const response = await fetch('support-optener');
        const data = await response.json();

        tickets.value = data
          .map((ticket) => ({
            id: ticket.id,
            tic_titulo: ticket.tic_titulo,
            tic_descripcion: ticket.tic_descripcion,
            tic_estado: ticket.tic_estado,
            prioridad: ticket.prioridad ? ticket.prioridad.pri_nombre : 'N/A',
            categoria: ticket.categoria ? ticket.categoria.cat_nombre : 'N/A',
            created_at: ticket.created_at,
          }))
          .sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); // Orden descendente por fecha
      } catch (error) {
        console.error('Error al recargar los tickets:', error);
      }
    };

    onMounted(recargarTickets);

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
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
        });

        if (!response.ok) {
          throw new Error('Error al aceptar el ticket');
        }

        const data = await response.json();

        if (data.status) {
          await recargarTickets(); // Recargar todos los tickets después de aceptar uno
          await nextTick(); // Asegurar que Vue procesa el renderizado
        } else {
          console.error(data.msg);
        }
      } catch (error) {
        console.error('Error al aceptar el ticket:', error);
      }
    };

    const finalizarTicket = async (ticket) => {
      try {
        const response = await fetch(`/soporte/tickets/finalizar/${ticket.id}`, {
          method: 'POST', // Usa POST según tu configuración de rutas
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
        });

        if (!response.ok) {
          throw new Error('Error al finalizar el ticket');
        }

        const data = await response.json();

        if (data.status) {
          await recargarTickets(); // Recargar todos los tickets después de finalizar uno
          await nextTick(); // Asegurar que Vue procesa el renderizado
        } else {
          console.error(data.msg);
        }
      } catch (error) {
        console.error('Error al finalizar el ticket:', error);
      }
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
      isTableView,
      toggleView,
      verDetalles,
      cerrarModal,
      mostrarModal,
      ticketSeleccionado,
      formFieldsVer,
      aceptarTicket,
      finalizarTicket,
      buscarQuery, // Retornamos buscarQuery
      filtrarTickets, // Retornamos filtrarTickets
      getEstadoLabelClass, // Retornamos la función para obtener clases de estado
    };
  },
};
</script>
