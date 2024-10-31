<script setup>
import { ref, onMounted, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
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
    prioridades: {
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
    selectedPrioridadId: {
        type: Number,
        default: null,
    },
});

const emit = defineEmits(["cerrar", "crear", "actualizar"]);

const formData = ref({
    sop_id: props.selectedSoporteId || "",
    pri_id: props.selectedPrioridadId || "",
    tic_id: props.ticketId,
});
const errores = ref({
    sop_id: "",
    pri_id: "",
});
const loading = ref(false);
const esActualizar = ref(!!props.selectedSoporteId);

const initialSoporteId = ref(props.selectedSoporteId || "");
const initialPrioridadId = ref(props.selectedPrioridadId || "");

onMounted(() => {
    initialSoporteId.value = formData.value.sop_id;
    initialPrioridadId.value = formData.value.pri_id;
});

const asignarSoporte = async () => {
    errores.value = { sop_id: "", pri_id: "" };

    if (!formData.value.sop_id) {
        errores.value.sop_id = "El campo Soporte es requerido.";
    }
    if (!formData.value.pri_id) {
        errores.value.pri_id = "El campo Prioridad es requerido.";
    }

    if (errores.value.sop_id || errores.value.pri_id) {
        return;
    }

    loading.value = true;

    try {
        const method = esActualizar.value ? "put" : "post";
        const endpoint = esActualizar.value
            ? `/tickets/${props.ticketId}/actualizar`
            : `/tickets/${props.ticketId}/asignar`;

        const requestData = {
            sop_id: formData.value.sop_id,
            pri_id: formData.value.pri_id,
            es_asignado: true,
            tic_estado: "Asignado",
        };

        const response = await axios[method](endpoint, requestData, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        let message = "¡";
        const soporteChanged = initialSoporteId.value !== formData.value.sop_id;
        const prioridadChanged =
            initialPrioridadId.value !== formData.value.pri_id;

        if (esActualizar.value) {
            if (soporteChanged && prioridadChanged) {
                message += "¡Soporte y Prioridad reasignados exitosamente!";
            } else if (soporteChanged) {
                message += "¡Soporte reasignado exitosamente!";
            } else if (prioridadChanged) {
                message += "¡Prioridad reasignada exitosamente!";
            }
        } else if (!esActualizar.value) {
            if (soporteChanged && prioridadChanged) {
                message += "¡Soporte y Prioridad asignados exitosamente!";
            } else if (soporteChanged) {
                message += "¡Soporte asignado exitosamente!";
            } else if (prioridadChanged) {
                message += "¡Prioridad asignada exitosamente!";
            }
        }
        message += "!";

        toast.success(message, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-green-500 p-2",
        });

        emit(esActualizar.value ? "actualizar" : "crear", response.data);
        emit("cerrar");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            console.error("Error inesperado:", error);
        }
        toast.error(`Error al asignar o actualizar al soporte técnico`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-red-500 p-2",
        });
    } finally {
        loading.value = false;
    }
};

const cerrarModal = () => emit("cerrar");
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-gray-400 bg-opacity-30"
    >
        <div class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg">
            <div class="border-2 border-blue-500 p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-blue-500">
                    {{ esActualizar ? "Actualizar" : "Asignar" }} {{ itemName }}
                </h2>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-gray-500">Soporte:</label>
                        <select
                            v-model="formData.sop_id"
                            class="w-full p-2 mb-1 border border-blue-500 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        >
                            <option value="" disabled>
                                Seleccione un soporte
                            </option>
                            <option
                                v-for="soporte in soportes"
                                :key="soporte.value"
                                :value="soporte.value"
                            >
                                {{ soporte.text }}
                            </option>
                        </select>
                        <p v-if="errores.sop_id" class="text-sm text-red-500">
                            {{ errores.sop_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block mb-2 text-gray-500"
                            >Prioridad:</label
                        >
                        <select
                            v-model="formData.pri_id"
                            class="w-full p-2 mb-1 border border-blue-500 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        >
                            <option value="" disabled>
                                Seleccione una prioridad
                            </option>
                            <option
                                v-for="prioridad in prioridades"
                                :key="prioridad.value"
                                :value="prioridad.value"
                            >
                                {{ prioridad.text }}
                            </option>
                        </select>
                        <p v-if="errores.pri_id" class="text-sm text-red-500">
                            {{ errores.pri_id }}
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
