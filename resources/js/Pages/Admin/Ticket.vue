<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import listTicket from "@/Components/Admin/listTicket.vue";
</script>

<template>
    <AppLayout title="Tickets">
        <div class="py-2">
            <listTicket />
        </div>
    </AppLayout>
</template>
