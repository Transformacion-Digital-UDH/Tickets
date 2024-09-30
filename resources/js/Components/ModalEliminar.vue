<script setup>
import ButtonEliminar from "@/Components/ButtonEliminar.vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";

const props = defineProps({
    item: Object,
    itemName: String,
    fieldName: String,
});

const emit = defineEmits(["confirmar", "cancelar"]);
</script>

<template>
    <div class="fixed inset-0 flex items-center justify-center transition-opacity bg-gray-400 bg-opacity-30">
        <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg">
            <div class="p-4 border-2 border-red-500 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-red-500">
                    Eliminar {{ itemName }}
                </h2>
                <p>
                    ¿Estás seguro de que deseas eliminar
                    <strong>{{ item[fieldName] }}</strong>?
                </p>
                <p class="mt-2 text-sm text-gray-500">
                    Esta acción eliminará permanentemente su {{ itemName }}.
                </p>
                <div class="flex justify-end mt-6 space-x-4">
                    <ButtonEliminar @click="$emit('confirmar')" />
                    <ButtonCerrar @click="$emit('cancelar')" />
                </div>
            </div>
        </div>
    </div>
</template>
