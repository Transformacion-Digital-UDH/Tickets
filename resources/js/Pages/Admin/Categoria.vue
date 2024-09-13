<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import listCategoria from '@/Components/Admin/listCategoria.vue';
</script>

<template>
    <AppLayout title="Aulas">
        <div class="py-6">
            <listCategoria/>
        </div>
    </AppLayout>
</template>