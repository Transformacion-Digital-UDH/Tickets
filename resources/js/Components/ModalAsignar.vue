<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";

const props = defineProps({
    formFieldsAsignar: {
        type: Array,
        default: () => [],
    },
    endpoint: {
        type: String,
        required: true,
    },
    itemName: String,
    soportes: {
        type: Array,
        default: () => [],
    },
    ticketId: {
        type: Number,
        required: true,
    },
    selectedSoporteId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["cerrar", "crear", "actualizar"]);

const formData = ref({
    sop_id: props.selectedSoporteId || null,
    tic_id: props.ticketId,
});
const errores = ref([]);
const loading = ref(false);
const successMessage = ref("");
const esActualizar = ref(!!props.selectedSoporteId); // Detectar si es para actualizar

// Computed para filtrar campos visibles (excluir booleanos)
const visibleFields = computed(() => {
    return props.formFieldsAsignar.filter((field) => field.type !== "boolean");
});

// Cargar información inicial
onMounted(() => {
    initializeForm();
});

// Inicialización del formulario
const initializeForm = () => {
    if (esActualizar.value) {
        // Si ya hay un soporte asignado, lo cargamos
        formData.value.sop_id = props.selectedSoporteId;
    } else {
        // Si es un nuevo registro, reiniciar los valores
        formData.value = {
            sop_id: null,
            tic_id: props.ticketId,
            es_asignado: false,
        };
        props.formFieldsAsignar.forEach((field) => {
            if (field.type === "boolean") {
                formData.value[field.name] = true;
            } else if (field.name !== "sop_id") {
                formData.value[field.name] = field.default || "";
            }
        });
    }
};

// Monitorear cambios en la lista de soportes
watch(
    () => props.soportes,
    (newSoportes) => {
        if (newSoportes && newSoportes.length > 0) {
            props.formFieldsAsignar.forEach((field) => {
                if (field.name === "sop_id") {
                    field.options = newSoportes.map((soporte) => ({
                        value: soporte.value,
                        label: soporte.text,
                    }));
                }
            });
        }
    },
    { immediate: true }
);

const asignarSoporte = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = [];

    try {
        const method = esActualizar.value ? "put" : "post";
        const endpoint = esActualizar.value
            ? `/tickets/${props.ticketId}/actualizar`
            : `/tickets/${props.ticketId}/asignar`;

        const requestData = {
            sop_id: formData.value.sop_id,
            es_asignado: true,
            tic_estado: "En progreso",
        };

        const response = await axios[method](endpoint, requestData, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        // Emit an event to notify the parent that the assignment has been made
        if (!esActualizar.value) {
            emit("crear", response.data); // If it's a new assignment
            successMessage.value = "Soporte asignado exitosamente!";
        } else {
            emit("actualizar", response.data); // If updating an assignment
            successMessage.value = "Soporte actualizado exitosamente!";
        }

        // Close the modal and refresh the parent component's state
        emit("cerrar");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            console.error("Error inesperado:", error);
        }
    } finally {
        loading.value = false;
    }
};

// Función para cerrar el modal
const cerrarModal = () => emit("cerrar");
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-50"
    >
        <div class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg">
            <div class="border-2 border-blue-500 p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-blue-500">
                    {{ esActualizar ? "Actualizar" : "Asignar" }} a un
                    {{ itemName }}
                </h2>
                <p v-if="successMessage" class="mb-4 text-green-500">
                    {{ successMessage }}
                </p>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div v-for="(field, index) in visibleFields" :key="index">
                        <label
                            :for="field.name"
                            class="block mb-2 text-gray-500"
                        >
                            {{ field.label }}:
                        </label>

                        <template v-if="field.type === 'select'">
                            <select
                                :id="field.name"
                                :name="field.name"
                                v-model="formData[field.name]"
                                class="w-full p-2 mb-1 border border-blue-500 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            >
                                <option value="" disabled>
                                    Seleccione su {{ field.label }}
                                </option>
                                <option
                                    v-for="option in field.options"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                        </template>
                        <p
                            v-if="errores[field.name]"
                            class="text-sm text-red-500"
                        >
                            {{ errores[field.name][0] }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <ButtonCrearActualizar
                        @click="asignarSoporte"
                        :loading="loading"
                        :itemName="esActualizar ? 'Actualizar' : 'Asignar'"
                    />
                    <ButtonCerrar @click="cerrarModal" />
                </div>
            </div>
        </div>
    </div>
</template>
