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
        getImage(sedeId) {
            const images = {
                1: "/images/sede1.jpg",
                2: "/images/sede2.jpg",
                3: "/images/sede3.jpg",
            };
            return images[sedeId] || "/images/default.jpg";
        },
        selectSede(sede) {
            alert(`Sede seleccionada: ${sede.sed_nombre}`);
            window.location.href = "/dashboard";
        },
    },
};
</script>

<template>
    <div class="container p-8 mx-auto">
        <h1 class="mb-8 text-3xl font-bold text-center">Elige tu Sede</h1>
        <div
            class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 justify-items-center"
        >
            <div
                v-for="sede in sedes"
                :key="sede.id"
                class="w-64 overflow-hidden transition-transform duration-300 transform bg-white rounded-lg shadow-lg cursor-pointer hover:scale-105"
                @click="selectSede(sede)"
            >
                <img
                    :src="getImage(sede.id)"
                    alt="Sede Image"
                    class="object-cover w-full h-40"
                />
                <div class="p-4 text-center">
                    <h2 class="mb-2 text-xl font-semibold">
                        {{ sede.sed_nombre }}
                    </h2>
                    <p class="text-gray-600">{{ sede.sed_direccion }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
