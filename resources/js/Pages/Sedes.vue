<template>
    <div class="sedes-container">
      <div class="sedes-table">
        <div class="card" v-for="sede in sedes" :key="sede.id">
          <img :src="sede.image" alt="Imagen de la sede" class="sede-image" />
          <div class="header">
            <h3>{{ sede.name }}</h3>
            <p class="location">{{ sede.location }}</p>
          </div>
          <div class="body">
            <p>{{ sede.description }}</p>
          </div>
          <div class="footer">
            <button class="btn">{{ sede.buttonText }}</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        sedes: [
          {
            id: 1,
            name: "Sede Central",
            location: "Ciudad Principal",
            description: "Oficinas y laboratorios principales.",
            image: "https://via.placeholder.com/300x200?text=Sede+Central",
            buttonText: "Ver Detalles",
          },
          {
            id: 2,
            name: "Sede Norte",
            location: "Suburbios del Norte",
            description: "Sede de programas tecnológicos.",
            image: "https://via.placeholder.com/300x200?text=Sede+Norte",
            buttonText: "Ver Detalles",
          },
          {
            id: 3,
            name: "Sede Sur",
            location: "Distrito Sur",
            description: "Enfocada en ciencias de la salud.",
            image: "https://via.placeholder.com/300x200?text=Sede+Sur",
            buttonText: "Ver Detalles",
          },
        ],
      };
    },
  };
  </script>
  
  <style scoped>
  .sedes-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Altura mínima para centrar verticalmente */
  }
  
  .sedes-table {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
  }
  
  .card {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    width: 300px;
    text-align: center;
    overflow: hidden;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
    cursor: pointer;
  }
  
  .card:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    transform: scale(1.05); /* Efecto de zoom */
  }
  
  .sede-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease; /* Animación para la imagen */
  }
  
  .card:hover .sede-image {
    transform: scale(1.1); /* Zoom en la imagen */
  }
  
  .header {
    padding: 15px;
  }
  
  .header h3 {
    margin: 0;
    font-size: 22px;
  }
  
  .location {
    font-size: 14px;
    color: #6c757d;
    margin-top: 5px;
  }
  
  .body {
    padding: 10px 15px;
    font-size: 14px;
    color: #333;
  }
  
  .footer {
    padding: 10px 15px;
  }
  
  .btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
  }
  
  .btn:hover {
    background-color: #0056b3;
  }
  </style>
  