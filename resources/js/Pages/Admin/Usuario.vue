<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import listUsuario from "@/Components/Admin/listUsuario.vue";
</script>

<template>
    <AppLayout v-if="$page.props.auth.user && $page.props.auth.user.rol && $page.props.auth.user.rol.name === 'Admin'" title="Sedes">
        <div class="py-2">
            <listUsuario />
        </div>
    </AppLayout>
</template>
