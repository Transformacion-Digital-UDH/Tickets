<script setup>
import { h } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import AppLayout from "@/Layouts/AppLayout.vue";
import StarMessage from "@/Components/StarMessage.vue";
import ListHistorial from "@/Components/Soporte/listHistorial.vue";

const props = defineProps({
    success: String,
});

if (props.success) {
    toast.success(
        {
            render: () => h(StarMessage, { message: props.success }),
        },
        {
            autoClose: 4000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-4",
        }
    );
}
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-2">
            <ListHistorial />
        </div>
    </AppLayout>
</template>
