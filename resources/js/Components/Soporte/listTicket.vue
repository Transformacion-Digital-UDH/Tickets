<template>
  <div class="p-4">
    <h1 class="text-xl font-bold text-center mb-4">Tickets Asignados</h1>

    <!-- Botón para cambiar la vista -->
    <div class="text-right mb-4">
      <button
        @click="toggleView"
        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-300"
      >
        {{ isTableView ? 'Vista de Tarjetas' : 'Vista de Tabla' }}
      </button>
    </div>

    <!-- Mostrar mensaje de éxito si existe -->
    <div v-if="success" class="alert alert-success mb-4 text-center text-green-600">
      {{ success }}
    </div>

    <!-- Vista de Tarjetas -->
    <div
      v-if="!isTableView && tickets.length > 0"
      class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
    >
      <div v-for="ticket in tickets" :key="ticket.id" class="relative w-full">
        <span
          class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg"
          :class="{
            'bg-orange-600': ticket.tic_estado === 'Abierto',
            'bg-gray-600': ticket.tic_estado === 'Asignado',
            'bg-blue-600': ticket.tic_estado === 'En progreso',
            'bg-green-600': ticket.tic_estado === 'Resuelto',
            'bg-red-600': ticket.tic_estado === 'Cerrado',
            'bg-yellow-600': ticket.tic_estado === 'Reabierto',
          }"
        ></span>
        <div
          class="relative h-full p-4 bg-white border-2 rounded-lg shadow-lg transition-shadow duration-300 hover:shadow-xl"
          :class="{
            'border-orange-600': ticket.tic_estado === 'Abierto',
            'border-gray-600': ticket.tic_estado === 'Asignado',
            'border-blue-600': ticket.tic_estado === 'En progreso',
            'border-green-600': ticket.tic_estado === 'Resuelto',
            'border-red-600': ticket.tic_estado === 'Cerrado',
            'border-yellow-600': ticket.tic_estado === 'Reabierto',
          }"
        >
          <div
            class="absolute px-3 py-1 text-xs font-semibold transition-colors duration-300 rounded-full shadow-md top-2 right-2"
            :class="{
              'bg-orange-600 text-white': ticket.tic_estado === 'Abierto',
              'bg-gray-600 text-white': ticket.tic_estado === 'Asignado',
              'bg-blue-600 text-white': ticket.tic_estado === 'En progreso',
              'bg-green-600 text-white': ticket.tic_estado === 'Resuelto',
              'bg-red-600 text-white': ticket.tic_estado === 'Cerrado',
              'bg-yellow-600 text-white': ticket.tic_estado === 'Reabierto',
            }"
          >
            {{ ticket.tic_estado }}
          </div>

          <div class="mt-4">
            <h2 class="text-lg font-bold text-gray-800 uppercase truncate">
              {{ ticket.tic_titulo }}
            </h2>
            <hr class="my-2 border-gray-200" />
          </div>

          <div class="flex flex-col space-y-2 text-sm text-gray-600">
            <span><strong>Prioridad:</strong> {{ ticket.prioridad?.nombre || 'N/A' }}</span>
            <span><strong>Categoría:</strong> {{ ticket.categoria?.nombre || 'N/A' }}</span>
            <span><strong>Creado el:</strong> {{ new Date(ticket.created_at).toLocaleDateString() }}</span>
          </div>

          <div class="flex justify-end mt-auto space-x-3">
            <button
              v-if="['Abierto', 'Asignado', 'Reabierto'].includes(ticket.tic_estado)"
              @click="$emit('asign', ticket)"
              class="text-blue-500 hover:text-blue-700 transition-colors duration-200"
            >
              <i class="mr-1 fas fa-user-plus"></i>
            </button>
            <button
              @click="$emit('view', ticket)"
              class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
            >
              <i class="mr-1 fas fa-eye"></i>
            </button>
            <button
              @click="$emit('edit', ticket)"
              class="text-green-500 hover:text-green-700 transition-colors duration-200"
            >
              <i class="mr-1 fas fa-edit"></i>
            </button>
            <button
              @click="$emit('eliminar', ticket)"
              class="text-red-500 hover:text-red-700 transition-colors duration-200"
            >
              <i class="mr-1 fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Vista de Tabla -->
    <div v-else-if="isTableView && tickets.length > 0" class="overflow-x-auto">
      <table class="min-w-full bg-white border">
        <thead>
          <tr>
            <th class="px-6 py-3 border-b-2 text-left">Título</th>
            <th class="px-6 py-3 border-b-2 text-left">Estado</th>
            <th class="px-6 py-3 border-b-2 text-left">Prioridad</th>
            <th class="px-6 py-3 border-b-2 text-left">Categoría</th>
            <th class="px-6 py-3 border-b-2 text-left">Fecha de Creación</th>
            <th class="px-6 py-3 border-b-2 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in tickets" :key="ticket.id">
            <td class="px-6 py-4 border-b">{{ ticket.tic_titulo }}</td>
            <td class="px-6 py-4 border-b">{{ ticket.tic_estado }}</td>
            <td class="px-6 py-4 border-b">{{ ticket.prioridad?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 border-b">{{ ticket.categoria?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 border-b">{{ new Date(ticket.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 border-b space-x-2">
              <button
                @click="$emit('view', ticket)"
                class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
              >
                <i class="fas fa-eye"></i>
              </button>
              <button
                @click="$emit('edit', ticket)"
                class="text-green-500 hover:text-green-700 transition-colors duration-200"
              >
                <i class="fas fa-edit"></i>
              </button>
              <button
                @click="$emit('eliminar', ticket)"
                class="text-red-500 hover:text-red-700 transition-colors duration-200"
              >
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mostrar mensaje si no hay tickets asignados -->
    <div v-else class="py-3 text-xs text-center text-gray-500 sm:text-sm">
      <p>No se encontraron resultados.</p>
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
    const isTableView = ref(false); // Estado para alternar la vista

    const toggleView = () => {
      isTableView.value = !isTableView.value;
    };

    // Obtener los tickets asignados al soporte al montar el componente
    onMounted(async () => {
      try {
        const response = await fetch('support-optener');
        const data = await response.json();
        tickets.value = data;
      } catch (error) {
        console.error('Error al obtener los tickets:', error);
      }
    });

    return { tickets, isTableView, toggleView };
  },
};
</script>
