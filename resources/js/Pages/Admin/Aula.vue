<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import listAula from '@/Components/Admin/listAula.vue';
</script>

<template>
    <AppLayout title="Aulas">
        <div class="py-2">
            <listAula />
        </div>
    </AppLayout>
</template>