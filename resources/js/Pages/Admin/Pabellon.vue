<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import listPabellon from "@/Components/Admin/listPabellon.vue";
</script>

<template>
    <AppLayout v-if="$page.props.auth.user && $page.props.auth.user.rol && $page.props.auth.user.rol.name === 'Admin'" title="Pabellones">
        <div class="py-2">
            <listPabellon />
        </div>
    </AppLayout>
</template>
