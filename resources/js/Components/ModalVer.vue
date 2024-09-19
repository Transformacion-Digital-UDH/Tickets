<script setup>
import { ref, watch, computed } from "vue";
import ButtonCerrar from "./ButtonCerrar.vue";

const props = defineProps({
    item: Object,
    itemName: String,
    formFieldsVer: Array,
    mostrarModalDetalles: Boolean,
});

const formData = ref({});

const visibleFields = computed(() => {
    return props.formFieldsVer.filter(field => field.type !== 'boolean');
});

watch(
    () => props.item,
    (newItem) => {
        if (newItem) {
            formData.value = { ...newItem };
        }
    },
    { immediate: true }
);

const emit = defineEmits(["close"]);

const cerrarDetallesModal = () => {
    emit("close");
};
</script>

<template>
    <div v-if="mostrarModalDetalles" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg">
            <div class="border-2 border-gray-400 p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-gray-600">
                    Detalles {{ itemName }}
                </h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b-2 pr-10 border-gray-400 py-2 text-left text-gray-600">Campo</th>
                            <th class="border-b-2 border-gray-400 py-2 text-left text-gray-600">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(field, index) in visibleFields" :key="index" class="border-b border-gray-400">
                            <td class="py-2 text-left pr-10 text-gray-500 font-semibold">{{ field.label }}</td>
                            <td class="py-2 text-left text-gray-600">{{ item[field.name] || "No disponible" }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-end mt-4">
                    <ButtonCerrar @click="cerrarDetallesModal" />
                </div>
            </div>
        </div>
    </div>
</template>