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

<template>
    <div>
        <div v-if="success" class="alert alert-success">
            {{ success }}
        </div>
        <div
            v-if="tickets.length > 0"
            class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:grid-cols-5"
        >
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
                        <span>
                            <strong>Creado el:</strong>
                            {{ new Date(ticket.created_at).toLocaleDateString() }}
                        </span>
                        <span>
                            <strong>Actualizado el:</strong>
                            {{ new Date(ticket.updated_at).toLocaleDateString() }}
                        </span>
                    </div>
                    <div class="flex mt-4 space-x-1 justify-end">
                        <button
                            v-if="
                                ticket.tic_estado === 'Abierto' ||
                                ticket.tic_estado === 'Asignado' ||
                                ticket.tic_estado === 'Reabierto'
                            "
                            @click="$emit('asign', ticket)"
                            class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-blue-300 to-blue-500 hover:from-blue-400 hover:to-blue-600"
                            title="Asignar"
                        >
                            <i class="mr-1 fas fa-user-plus"></i>
                        </button>
                        <button
                            @click="$emit('view', ticket)"
                            class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-gray-300 to-gray-500 hover:from-gray-400 hover:to-gray-600"
                            title="Ver detalles"
                        >
                            <i class="mr-1 fas fa-eye"></i>
                        </button>
                        <button
                            @click="$emit('edit', ticket)"
                            class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                            title="Editar"
                        >
                            <i class="mr-1 fas fa-edit"></i>
                        </button>
                        <button
                            @click="$emit('eliminar', ticket)"
                            class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                            title="Eliminar"
                        >
                            <i class="mr-1 fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            colspan="7"
            class="py-3 text-xs text-left text-gray-500 sm:text-sm"
        >
            <p>No se encontraron resultados.</p>
        </div>
    </div>
</template>
