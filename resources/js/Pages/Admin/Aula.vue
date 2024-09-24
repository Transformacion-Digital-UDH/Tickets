<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import listAula from '@/Components/Admin/listAula.vue';
</script>

<template>
    <AppLayout v-if="$page.props.auth.user && $page.props.auth.user.rol && $page.props.auth.user.rol.name === 'Admin'" title="Aulas">
        <div class="py-2">
            <listAula />
        </div>
    </AppLayout>
</template>