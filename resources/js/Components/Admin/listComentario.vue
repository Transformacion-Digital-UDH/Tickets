<script setup>
import { ref } from "vue";


const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
    comentarios: {
        type: Array,
        default: () => [],
    },
});
const emit = defineEmits(["comentario-agregado"]);

const nuevoComentario = ref("");
const error = ref(null);


</script>

<template>
    
</template>
