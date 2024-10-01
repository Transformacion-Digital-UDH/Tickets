<script setup>
import { ref, onMounted, watch, computed } from "vue";
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
const esActualizar = ref(!!props.selectedSoporteId);

const visibleFields = computed(() => {
    return props.formFieldsAsignar.filter((field) => field.type !== "boolean");
});

onMounted(() => {
    initializeForm();
});

const initializeForm = () => {
    if (esActualizar.value) {
        formData.value.sop_id = props.selectedSoporteId;
    } else {
        formData.value = {
            sop_id: "",
            tic_id: props.ticketId,
            es_asignado: false,
        };
    }
};

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
            tic_estado: "Asignado",
        };

        const response = await axios[method](endpoint, requestData, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });

        if (!esActualizar.value) {
            toast.success(`Soporte asignado exitosamente!`, {
                autoClose: 3000,
                position: "bottom-right",
                style: { width: "400px" },
                className: "border-l-4 border-green-500 p-2",
            });
            emit("crear", response.data);
        } else {
            toast.success(`Soporte actualizado exitosamente!`, {
                autoClose: 3000,
                position: "bottom-right",
                style: { width: "400px" },
                className: "border-l-4 border-green-500 p-2",
            });
            emit("actualizar", response.data);
        }
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
