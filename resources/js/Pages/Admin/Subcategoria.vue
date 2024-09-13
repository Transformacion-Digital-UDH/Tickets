<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import listSubcategoria from '@/Components/Admin/listSubcategoria.vue';
</script>

<template>
    <AppLayout title="Aulas">
        <div class="py-6">
            <listSubcategoria/>
        </div>
    </AppLayout>
</template>