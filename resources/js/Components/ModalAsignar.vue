<script setup>
import { ref, onMounted, watch, computed } from "vue";
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
});

const emit = defineEmits(["cerrar", "crear"]);

const formData = ref({});
const errores = ref([]);
const loading = ref(false);
const successMessage = ref("");

const visibleFields = computed(() => {
    return props.formFieldsAsignar.filter((field) => field.type !== "boolean");
});

onMounted(() => {
    props.formFieldsAsignar.forEach((field) => {
        if (field.type === "boolean") {
            formData.value[field.name] = true;
        } else {
            formData.value[field.name] = field.default || "";
        }
    });
});

watch(
    () => props.soportes,
    (newSoportes) => {
        if (newSoportes && newSoportes.length > 0) {
            props.formFieldsAsignar.forEach((field) => {
                if (field.name === "use_id") {
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

const submitForm = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = [];
    try {
        const response = await axios.post(props.endpoint, formData.value, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        emit("crear", response.data);
        successMessage.value = "Creación exitosa!";
        props.formFieldsAsignar.forEach((field) => {
            if (field.type === "boolean") {
                formData.value[field.name] = true;
            } else {
                formData.value[field.name] = field.default || "";
            }
        });
        emit("cerrar");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            console.error("Error:", error);
        }
    } finally {
        loading.value = false;
    }
};

const cerrarModal = () => emit("cerrar");
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-50"
    >
        <div class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg">
            <div class="border-2 border-blue-500 p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-blue-500">
                    Asignar a un {{ itemName }}
                </h2>
                <p v-if="successMessage" class="mb-4 text-green-500">
                    {{ successMessage }}
                </p>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div v-for="(field, index) in visibleFields" :key="index">
                        <label
                            :for="field.name"
                            class="block mb-2 text-gray-500"
                            >{{ field.label }}:</label
                        >

                        <template v-if="field.type === 'select'">
                            <select
                                :id="field.name"
                                :name="field.name"
                                v-model="formData[field.name]"
                                :autocomplete="field.autocomplete || 'off'"
                                :class="{
                                    'text-blue-500':
                                        formData[field.name] === '',
                                    'text-gray-900':
                                        formData[field.name] !== '',
                                }"
                                class="w-full p-2 mb-1 placeholder-blue-500 border border-blue-500 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                            >
                                <option value="" disabled selected>
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
                        @click="submitForm"
                        :loading="loading"
                        :itemName="'Asignar'"
                    />
                    <ButtonCerrar @click="cerrarModal" />
                </div>
            </div>
        </div>
    </div>
</template>
