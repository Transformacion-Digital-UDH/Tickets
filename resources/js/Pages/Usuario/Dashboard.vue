<script setup>
import { h } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { ref, onMounted } from "vue";
import MobileLayout from "@/Layouts/MobileLayout.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import listDashboard from "@/Components/Usuario/listDashboard.vue";
import StarMessage from "@/Components/StarMessage.vue";

const props = defineProps({
    success: String,
});

const isMobile = ref(false);

const updateIsMobile = () => {
    isMobile.value = window.innerWidth <= 768;
};

onMounted(() => {
    updateIsMobile();
    window.addEventListener("resize", updateIsMobile);
});

if (props.success) {
    toast.success(
        {
            render: () => h(StarMessage, { message: props.success }),
        },
        {
            autoClose: 4000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        }
    );
}
</script>

<template>
    <component
        :is="isMobile ? MobileLayout : AppLayout"
        v-if="
            $page.props.auth.user &&
            $page.props.auth.user.rol &&
            $page.props.auth.user.rol.name === 'Usuario'
        "
        title="Dashboard"
    >
        <div class="py-2">
            <listDashboard />
        </div>
    </component>

    <div v-else class="flex items-center justify-center min-h-screen bg-white">
        <h1 class="text-xl font-semibold text-gray-800">
            No tienes acceso a esta página o no estás autenticado.
        </h1>
    </div>
</template>
