<template>
    <div class="container w-full p-8 background">
        <h1 class="mb-8 text-3xl font-bold text-center text-white">
            Elige tu Sede
        </h1>
        <div class="cards-grid">
            <div
                v-for="sede in sedes"
                :key="sede.id"
                class="card rgb"
                @click="selectSede(sede)"
            >
                <div class="card-image"></div>
                <div class="p-4 text-center card-content">
                    <h2 class="mb-2 text-xl font-semibold">
                        {{ sede.sed_nombre }}
                    </h2>
                    <p class="text-gray-400">{{ sede.sed_direccion }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            sedes: [],
        };
    },
    mounted() {
        this.fetchSedes();
    },
    methods: {
        async fetchSedes() {
            try {
                const response = await axios.get("/sedes");
                this.sedes = response.data;
            } catch (error) {
                console.error("Error al obtener las sedes:", error);
            }
        },
        selectSede(sede) {
            alert(`Sede seleccionada: ${sede.sed_nombre}`);
            window.location.href = "/dashboard";
        },
    },
};
</script>

<style scoped>
/* Aplicar fondo al body y html para cubrir todo el ancho */
html,
body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, #1a1a1a, #000);
}

/* Fondo oscuro con degradado */
.background {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Contenedor de las tarjetas */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(
        auto-fit,
        minmax(280px, 1fr)
    ); /* Asegurar un ancho mínimo */
    gap: 20px;
    justify-items: center;
    width: 100%;
}

/* Estilo de las tarjetas */
.card {
    display: grid;
    grid-template-rows: 210px 1fr 20px; /* Ajuste con altura flexible para el contenido */
    background: #1d1d1d;
    color: white;
    text-align: justify;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    width: 280px; /* Ancho fijo para las tarjetas */
    height: 400px; /* Altura fija para asegurar consistencia */
}

/* Efecto de borde RGB animado */
.rgb::after {
    content: "";
    background: linear-gradient(
        45deg,
        #ff0000 0%,
        #ff9a00 10%,
        #d0de21 20%,
        #4fdc4a 30%,
        #3fdad8 40%,
        #2fc9e2 50%,
        #1c7fee 60%,
        #5f15f2 70%,
        #ba0cf8 80%,
        #fb07d9 90%,
        #ff0000 100%
    );
    background-size: 300% 100%;
    position: absolute;
    inset: -3px;
    z-index: -1;
    border-radius: 12px;
    animation: rgb 3s ease infinite;
}

@keyframes rgb {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Fondo de las imágenes con degradado */
.card-image {
    background: linear-gradient(#fff0 0%, #fff0 70%, #1d1d1d 100%),
        url("img1.jpg"); /* Puedes usar una imagen por defecto o dinámicas */
    background-size: cover;
    background-position: center;
    height: 100%;
}

/* Estilo del contenido de la tarjeta */
.card-content h2 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 8px;
}

.card-content p {
    color: #bbb;
    font-size: 14px;
}

/* Efectos hover */
.card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.6);
}

/* Responsividad */
@media (max-width: 768px) {
    .card {
        max-width: 100%;
        grid-template-rows: 180px 1fr 20px; /* Ajuste en pantallas pequeñas */
        height: auto; /* Dejar altura ajustable en pantallas pequeñas */
    }
}
</style>
