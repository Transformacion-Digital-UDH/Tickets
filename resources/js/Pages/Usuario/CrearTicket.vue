<script setup>
import { ref, onMounted } from "vue";
import MobileLayout from "@/Layouts/MobileLayout.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import formTicket from "@/Components/Usuario/formTicket.vue";

const isMobile = ref(false);

const updateIsMobile = () => {
    isMobile.value = window.innerWidth <= 768;
};

onMounted(() => {
    updateIsMobile();
    window.addEventListener("resize", updateIsMobile);
});
</script>

<template>
    <component
        :is="isMobile ? MobileLayout : AppLayout"
        v-if="
            $page.props.auth.user &&
            $page.props.auth.user.rol &&
            $page.props.auth.user.rol.name === 'Usuario'
        "
        title="Crear Ticket"
    >
        <div class="py-2">
            <formTicket endpoint="/user-tickets/store" />
        </div>
    </component>

    <div v-else class="flex items-center justify-center min-h-screen bg-white">
        <h1 class="text-xl font-semibold text-gray-800">
            No tienes acceso a esta página o no estás autenticado.
        </h1>
    </div>
</template>
