<script setup>
import { ref, onMounted } from "vue";
import MobileLayout from "@/Layouts/MobileLayout.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import listSoporte from "@/Components/Admin/listSoporte.vue";

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
    setTimeout(() => {
        window.location.reload();
    }, 10);
}
</script>

<template>
    <component
        :is="isMobile ? MobileLayout : AppLayout"
        v-if="
            $page.props.auth.user &&
            $page.props.auth.user.rol &&
            $page.props.auth.user.rol.name === 'Admin'
        "
        title="Soportes"
    >
        <div class="py-2">
            <listSoporte />
        </div>
    </component>

    <div v-else class="flex items-center justify-center min-h-screen bg-white">
        <h1 class="text-xl font-semibold text-gray-800">
            No tienes acceso a esta página o no estás autenticado.
        </h1>
    </div>
</template>
