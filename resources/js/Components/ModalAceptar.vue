<script setup>
const props = defineProps({
    visible: Boolean,
});

const emit = defineEmits(["confirm", "cancel"]);

const confirm = () => {
    emit("confirm");
};

const cancel = () => {
    emit("cancel");
};
</script>

<template>
    <div v-if="visible" class="fixed inset-0 flex items-center justify-center transition-opacity bg-gray-400 bg-opacity-30 z-50">
        <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg z-50">
            <div class="p-4 border-2 border-yellow-500 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-yellow-500">Advertencia</h2>
                <p>¿Está seguro de que desea aceptar el ticket?</p>
                <div class="flex justify-end mt-6 space-x-4">
                    <button @click="confirm" class="px-4 py-2 font-semibold text-white bg-yellow-500 rounded hover:bg-yellow-600">Sí, ¡Estoy seguro!</button>
                    <button @click="cancel" class="px-4 py-2 font-semibold text-gray-700 bg-gray-200 rounded hover:bg-gray-300">No</button>
                </div>
            </div>
        </div>
    </div>
</template>