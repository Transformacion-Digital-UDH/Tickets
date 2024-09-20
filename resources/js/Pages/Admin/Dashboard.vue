<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import listDashboard from "@/Components/Admin/listDashboard.vue";
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-2">
            <listDashboard />
        </div>
    </AppLayout>
</template>
