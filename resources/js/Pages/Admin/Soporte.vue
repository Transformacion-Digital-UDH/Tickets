<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import listSoporte from "@/Components/Admin/listSoporte.vue";
</script>

<template>
    <AppLayout v-if="$page.props.auth.user && $page.props.auth.user.rol && $page.props.auth.user.rol.name === 'Admin'" title="Sedes">
        <div class="py-2">
            <listSoporte />
        </div>
    </AppLayout>
</template>
