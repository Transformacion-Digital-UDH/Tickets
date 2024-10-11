<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

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
                this.sedes = response.data.map((sede) => ({
                    id: sede.id,
                    name: sede.sed_nombre,
                    location: sede.sed_ciudad,
                    description: sede.sed_direccion,
                    image: sede.sed_imagen
                        ? `/storage/${sede.sed_imagen}`
                        : "https://via.placeholder.com/300x200?text=Sin+Imagen",
                    buttonText: "Seleccionar Sede",
                }));
            } catch (error) {
                console.error("Error al cargar las sedes:", error);
                toast.error("Error al cargar las sedes.");
            }
        },

        async seleccionarSede(sede) {
            try {
                const response = await axios.post("/registrar-sede", {
                    sede_id: sede.id,
                });

                toast.success("Sede seleccionada correctamente.");

                if (response.data.redirectUrl) {
                    window.location.href = response.data.redirectUrl;
                }
            } catch (error) {
                console.error("Error al seleccionar la sede:", error);
                toast.error("Hubo un error al seleccionar la sede.");
            }
        },
    },
};
</script>

<template>
    <div class="sedes-container">
        <div class="sedes-table">
            <div
                class="card"
                v-for="sede in sedes"
                :key="sede.id"
                @click="seleccionarSede(sede)"
            >
                <img
                    :src="sede.image"
                    alt="Imagen de la sede"
                    class="sede-image"
                />
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

<style scoped>
.sede-image {
    width: 100%;
    height: 200px;
    object-fit: contain;
    border-radius: 5px;
    /* Ensure that the image fits the card properly */
}

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
