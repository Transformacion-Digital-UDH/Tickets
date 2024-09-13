<script setup>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTimes, faCheck } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(faTimes, faCheck);

const props = defineProps({
    item: Object,
    itemName: String,
    fieldName: String,
});

const emit = defineEmits(["confirmar", "cancelar"]);
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-50"
    >
        <div
            class="w-full max-w-lg p-6 transition-transform transform bg-white rounded-lg shadow-lg"
        >
            <h2 class="mb-4 text-xl font-bold text-gray-600">
                Activar {{ itemName }}
            </h2>
            <p>
                ¿Estás seguro de que deseas activar
                <strong>{{ item[fieldName] }}</strong
                >?
            </p>
            <p class="mt-2 text-sm text-gray-500">
                Esta acción hará que {{ itemName }} esté disponible nuevamente
                para su uso.
            </p>
            <div class="flex justify-end mt-6 space-x-4">
                <button
                    @click="$emit('confirmar')"
                    class="flex justify-center items-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gradient-to-r from-green-300 to-green-500 rounded-lg shadow-md hover:from-green-400 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
                >
                    <font-awesome-icon icon="check" class="mr-2 text-lg" />
                    Activar
                </button>
                <button
                    @click="$emit('cancelar')"
                    class="flex justify-center px-4 py-2.5 text-sm font-semibold text-white transition-all duration-300 bg-gradient-to-r from-gray-300 to-gray-400 rounded-lg shadow-md hover:from-gray-300 hover:to-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50"
                >
                    <font-awesome-icon icon="times" class="mr-2 text-lg" />
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>
