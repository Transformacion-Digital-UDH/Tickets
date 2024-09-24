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
          <p class="status">+10 since last update</p>
        </div>
      </div>
  
      <!-- Tarjeta Pendientes -->
      <div class="card">
        <div class="icon-container">
          <i class="fas fa-hourglass-half icon"></i>
        </div>
        <div class="content">
          <h3>Pendientes</h3>
          <p class="number">{{ pendingTickets }}</p>
          <p class="status">+5 since last update</p>
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
          <p class="status">+3 since last update</p>
        </div>
      </div>
  
      <!-- Tarjeta Urgentes -->
      <div class="card">
        <div class="icon-container">
          <i class="fas fa-exclamation-triangle icon"></i>
        </div>
        <div class="content">
          <h3>Urgentes</h3>
          <p class="number">{{ urgentTickets }}</p>
          <p class="status">+2 since last update</p>
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
          <p class="status">+1 since last update</p>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        assignedTickets: 120,
        pendingTickets: 45,
        inProgressTickets: 30,
        urgentTickets: 10,
        todayTickets: 20,
      };
    }
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
  