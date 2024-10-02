<template>
    <div>
      <h1>Tickets Asignados</h1>
  
      <!-- Mostrar mensaje de éxito si existe -->
      <div v-if="success" class="alert alert-success">
        {{ success }}
      </div>
  
      <!-- Mostrar lista de tickets asignados al soporte -->
      <div v-if="tickets.length > 0" class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
        <div
          v-for="ticket in tickets"
          :key="ticket.id"
          class="relative w-full"
        >
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
            class="relative h-full p-6 bg-white border-2 rounded-lg"
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
                'bg-orange-600 text-white':
                  ticket.tic_estado === 'Abierto',
                'bg-gray-600 text-white':
                  ticket.tic_estado === 'Asignado',
                'bg-blue-600 text-white':
                  ticket.tic_estado === 'En progreso',
                'bg-green-600 text-white':
                  ticket.tic_estado === 'Resuelto',
                'bg-red-600 text-white':
                  ticket.tic_estado === 'Cerrado',
                'bg-yellow-600 text-white':
                  ticket.tic_estado === 'Reabierto',
              }"
            >
              {{ ticket.tic_estado }}
            </div>
            <div>
              <h2
                class="text-lg font-bold text-gray-800 uppercase truncate mt-2"
              >
                {{ ticket.cat_nombre }}
              </h2>
              <hr class="my-2 border-gray-200" />
            </div>
            <div class="flex flex-col space-y-2 text-sm text-gray-600">
              <span
                ><strong>Título:</strong>
                {{ ticket.tic_titulo || "N/A" }}</span
              >
              <span
                ><strong>Prioridad:</strong>
                {{ ticket.pri_nombre || "N/A" }}</span
              >
              <span>
                <strong>Soporte:</strong>
                {{ ticket.soporte_nombre || "N/A" }}
              </span>
            </div>
            <div class="flex justify-end mt-auto space-x-3">
              <button
                v-if="
                  ticket.tic_estado === 'Abierto' ||
                  ticket.tic_estado === 'Asignado' ||
                  ticket.tic_estado === 'Reabierto'
                "
                @click="$emit('asign', ticket)"
                class="text-blue-500 hover:text-blue-700"
              >
                <i class="mr-1 fas fa-user-plus"></i>
              </button>
              <button
                @click="$emit('view', ticket)"
                class="text-gray-500 hover:text-gray-700"
              >
                <i class="mr-1 fas fa-eye"></i>
              </button>
              <button
                @click="$emit('edit', ticket)"
                class="text-green-500 hover:text-green-700"
              >
                <i class="mr-1 fas fa-edit"></i>
              </button>
              <button
                @click="$emit('eliminar', ticket)"
                class="text-red-500 hover:text-red-700"
              >
                <i class="mr-1 fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Mostrar mensaje si no hay tickets asignados -->
      <div
        v-else
        colspan="7"
        class="py-3 text-xs text-left text-gray-500 sm:text-sm"
      >
        <p>No se encontraron resultados.</p>
      </div>
    </div>
  </template>
  
  <script setup>
  const props = defineProps({
    tickets: {
      type: Array,
      required: true,
    },
    success: String,
  });
  
  const emit = defineEmits(["asign", "view", "edit", "eliminar"]);
  </script>