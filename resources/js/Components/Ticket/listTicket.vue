<script>
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';
export default {
  data() {
    return {
      isCardView: true,
      mostrarModalCrearTicket: false,
      mostrarModalDetalles: false,
      mostrarModalAsignarSoporte: false,
      ticketSeleccionado: {},
      nuevoTicket: {
        titulo: '',
        descripcion: '',
        prioridad: '',
        usuario: '',
        categoria: ''
      },
      soporteAsignado: '',
      searchQuery: '', // Barra de búsqueda
      tickets: [],
      usuarios: [],
      prioridades: [],
      categorias: [],
      pabellones: [],
    };
  },
  computed: {
    filteredTickets() {
      return this.tickets.filter(ticket => {
        const search = this.searchQuery.toLowerCase();
        return (
          ticket.tic_titulo.toLowerCase().includes(search) ||
          (ticket.tic_descripcion && ticket.tic_descripcion.toLowerCase().includes(search)) ||
          (ticket.tic_estado && ticket.tic_estado.toLowerCase().includes(search)) ||
          (ticket.prioridad && ticket.prioridad.pri_nombre.toLowerCase().includes(search)) ||
          (ticket.user && ticket.user.name.toLowerCase().includes(search)) ||
          (ticket.categoria && ticket.categoria.cat_nombre.toLowerCase().includes(search))
        );
      });
    }
  }
  ,
  created() {
    this.fetchTickets();
    this.fetchPrioridades();
    this.fetchUsuarios();
    this.fetchCategorias();
    this.fetchPabellones();
  },


  methods: {
    async fetchTickets() {
      try {
        const response = await axios.get('/tickets');
        this.tickets = response.data;
      } catch (error) {
        console.error('Error al traer los tickets:', error);
      }
    },
    async fetchPrioridades() {
      try {
        const response = await axios.get('/prioridades');
        this.prioridades = response.data;
      } catch (error) {
        console.error('Error al traer las prioridades:', error);
      }
    },
    async fetchUsuarios() {
      try {
        const response = await axios.get('/usuarios');
        this.usuarios = response.data;
      } catch (error) {
        console.error('Error al traer los usuarios:', error);
      }
    },
    async fetchCategorias() {
      try {
        const response = await axios.get('/categorias');
        this.categorias = response.data;
      } catch (error) {
        console.error('Error al traer las categorías:', error);
      }
    },
    async fetchPabellones() {
      try {
        const response = await axios.get('/pabellons');
        this.pabellones = response.data;
      } catch (error) {
        console.error('Error al traer los pabellones:', error);
      }
    },
    async crearTicket() {
      try {
        const response = await axios.post('/tickets', {
          tic_titulo: this.nuevoTicket.titulo,
          tic_descripcion: this.nuevoTicket.descripcion,
          pri_id: this.nuevoTicket.prioridad,  // Se usa el valor del select de prioridad
          use_id: this.nuevoTicket.usuario,     // Se usa el valor del select de usuario
          cat_id: this.nuevoTicket.categoria,   // Se usa el valor del select de categoría
          pab_id: this.nuevoTicket.pabellon,    // Se usa el valor del select de pabellón
          tic_estado: 'Abierto',
          tic_activo: true
        });
        this.tickets.push(response.data);
        this.cerrarCrearTicketModal();
      } catch (error) {
        console.error('Error al crear ticket:', error);
      }
    },
    showCrearTicketModal() {
      this.mostrarModalCrearTicket = true;
    },
    cerrarCrearTicketModal() {
      this.mostrarModalCrearTicket = false;
    },
    showDetallesModal(ticket) {
      this.ticketSeleccionado = ticket;
      this.mostrarModalDetalles = true;
    },
    cerrarDetallesModal() {
      this.mostrarModalDetalles = false;
    },
    showAsignarSoporteModal(ticket) {
      this.ticketSeleccionado = ticket;
      this.mostrarModalAsignarSoporte = true;
    },
    cerrarAsignarSoporteModal() {
      this.mostrarModalAsignarSoporte = false;
    },
    asignarSoporte() {
      alert(`El ticket "${this.ticketSeleccionado.tic_titulo}" ha sido asignado a ${this.soporteAsignado}`);
      this.cerrarAsignarSoporteModal();
    },
  },
};

</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Lista de Tickets</h1>

    <!-- Barra de búsqueda -->
    <div class="mb-4">
      <input v-model="searchQuery" type="text"
        placeholder="Buscar por título, estado, prioridad, usuario o categoría..." class="border p-2 w-full rounded" />
    </div>

    <div class="mb-4 flex justify-end">
      <button @click="showCrearTicketModal"
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition duration-300 mr-2">
        Crear Ticket
      </button>
      <button @click="toggleView"
        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300">
        <i :class="isCardView ? 'fas fa-list' : 'fas fa-table'"></i>
      </button>
    </div>

    <!-- Vista en tarjetas -->
    <div v-if="isCardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="ticket in filteredTickets" :key="ticket.id"
        class="relative bg-white shadow-lg rounded-lg p-4 transition-transform transform hover:scale-105 min-h-[100px] max-h-[150px] flex flex-col justify-between">
        <div>
          <div class="absolute top-2 right-2 px-2 py-1 rounded text-xs" :class="{
            'bg-orange-700 text-white': ticket.tic_estado === 'Abierto',
            'bg-blue-700 text-white': ticket.tic_estado === 'En progreso',
            'bg-green-700 text-white': ticket.tic_estado === 'Finalizado',
            'bg-red-700 text-white': ticket.tic_estado === 'Cerrado',
          }">
            {{ ticket.tic_estado }}
          </div>
          <h2 class="text-xl font-semibold uppercase">{{ ticket.tic_titulo }}</h2>
          <hr>
          <!-- Prioridad, Usuario y Categoría alineados -->
          <div class="flex flex-col mt-4">
            <span class="text-sm text-gray-500">Prioridad: {{ ticket.prioridad ? ticket.prioridad.pri_nombre : ''}}</span>
            <span class="text-sm text-gray-500">Usuario: {{ ticket.user ? ticket.user.name : '' }}</span>
            <span class="text-sm text-gray-500">Categoría: {{ ticket.categoria ? ticket.categoria.cat_nombre : '' }}</span>
          </div>
        </div>
        <div class="flex justify-end items-center space-x-2">
          <button @click="showDetallesModal(ticket)" class="text-blue-500 hover:text-blue-700 transition duration-300">
            <i class="fas fa-eye"></i>
          </button>
          <button @click="showAsignarSoporteModal(ticket)"
            class="text-purple-500 hover:text-purple-700 transition duration-300">
            <i class="fas fa-user-plus"></i>
          </button>
        </div>
      </div>

    </div>

    <!-- Vista en tabla -->
    <div v-else>
      <table class="min-w-full bg-white border">
        <thead>
          <tr>
            <th class="px-4 py-2 border">Título</th>
            <th class="px-4 py-2 border">Descripción</th>
            <th class="px-4 py-2 border">Prioridad</th>
            <th class="px-4 py-2 border">Estado</th>
            <th class="px-4 py-2 border">Usuario</th>
            <th class="px-4 py-2 border">Categoría</th>
            <th class="px-4 py-2 border">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ticket in filteredTickets" :key="ticket.id" class="border-t">
            <td class="px-4 py-2 border">{{ ticket.tic_titulo }}</td>
            <td class="px-4 py-2 border">{{ ticket.tic_descripcion }}</td>
            <td class="px-4 py-2 border">{{ ticket.prioridad.pri_nombre }}</td>
            <td class="px-4 py-2 border">{{ ticket.tic_estado }}</td>
            <td class="px-4 py-2 border">{{ ticket.user.name }}</td>
            <td class="px-4 py-2 border">{{ ticket.categoria.cat_nombre }}</td>
            <td class="px-4 py-2 border flex justify-between space-x-2">
              <button @click="showDetallesModal(ticket)"
                class="text-blue-500 hover:text-blue-700 transition duration-300">
                <i class="fas fa-eye"></i>
              </button>
              <button @click="showAsignarSoporteModal(ticket)"
                class="text-purple-500 hover:text-purple-700 transition duration-300">
                <i class="fas fa-user-plus"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Crear Ticket -->
    <div v-if="mostrarModalCrearTicket" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-xl font-bold mb-4">Crear Nuevo Ticket</h2>
        <label class="block mb-2">Título:</label>
        <input type="text" v-model="nuevoTicket.titulo" class="border p-2 w-full rounded mb-4" />
        <label class="block mb-2">Descripción:</label>
        <textarea v-model="nuevoTicket.descripcion" class="border p-2 w-full rounded mb-4"></textarea>
        <label class="block mb-2">Prioridad:</label>
        <select v-model="nuevoTicket.prioridad" class="border p-2 w-full rounded mb-4">
          <option v-for="prioridad in prioridades" :key="prioridad.id" :value="prioridad.id">
            {{ prioridad.pri_nombre }}
          </option>
        </select>

        <label class="block mb-2">Usuario:</label>
        <select v-model="nuevoTicket.usuario" class="border p-2 w-full rounded mb-4">
          <option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id">
            {{ usuario.name }}
          </option>
        </select>

        <label class="block mb-2">Categoría:</label>
        <select v-model="nuevoTicket.categoria" class="border p-2 w-full rounded mb-4">
          <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
            {{ categoria.cat_nombre }}
          </option>
        </select>

        <label class="block mb-2">Pabellón:</label>
        <select v-model="nuevoTicket.pabellon" class="border p-2 w-full rounded mb-4">
          <option v-for="pabellon in pabellones" :key="pabellon.id" :value="pabellon.id">
            {{ pabellon.pab_nombre }}
          </option>
        </select>
        <button @click="crearTicket" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
          Crear
        </button>
        <button @click="cerrarCrearTicketModal" class="ml-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
          Cancelar
        </button>
      </div>
    </div>

    <!-- Modal Detalles Ticket -->
    <div v-if="mostrarModalDetalles" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-xl font-bold mb-4 uppercase">Detalles del Ticket</h2>
        <p><strong>Título:</strong> {{ ticketSeleccionado.tic_titulo }}</p>
        <p><strong>Descripción:</strong> {{ ticketSeleccionado.tic_descripcion }}</p>
        <p><strong>Prioridad:</strong> {{ ticketSeleccionado.prioridad.pri_nombre }}</p>
        <p><strong>Estado:</strong> {{ ticketSeleccionado.tic_estado }}</p>
        <p><strong>Usuario:</strong> {{ ticketSeleccionado.user.name }}</p>
        <p><strong>Categoría:</strong> {{ ticketSeleccionado.categoria.cat_nombre }}</p>
        <button @click="cerrarDetallesModal" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
          Cerrar
        </button>
      </div>
    </div>

    <!-- Modal Asignar Soporte -->
    <div v-if="mostrarModalAsignarSoporte"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
        <h2 class="text-xl font-bold mb-4">Asignar a Soporte</h2>
        <p><strong>Título:</strong> {{ ticketSeleccionado.tic_titulo }}</p>
        <label class="block mb-2">Asignar a:</label>
        <input type="text" v-model="soporteAsignado" class="border p-2 w-full rounded mb-4" />
        <button @click="asignarSoporte" class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
          Asignar
        </button>
        <button @click="cerrarAsignarSoporteModal"
          class="ml-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>
