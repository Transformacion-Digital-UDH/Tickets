<script setup>
import { ref, onMounted, watch, computed } from "vue";
import ButtonCrearActualizar from "./ButtonCrearActualizar.vue";
import ButtonCerrar from "./ButtonCerrar.vue";

const props = defineProps({
    formFields: {
        type: Array,
        default: () => [],
    },
    endpoint: {
        type: String,
        required: true,
    },
    itemName: String,
    sedes: {
        type: Array,
        default: () => [],
    },
    pabellons: {
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
    return props.formFields.filter(field => field.type !== 'boolean');
});

onMounted(() => {
    props.formFields.forEach((field) => {
        if (field.type === "boolean") {
            formData.value[field.name] = true;
        } else {
            formData.value[field.name] = field.default || "";
        }
    });
});

watch(
    () => props.sedes,
    (newSedes) => {
        if (newSedes && newSedes.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "sed_id") {
                    field.options = newSedes.map((sede) => ({
                        value: sede.value,
                        label: sede.text,
                    }));
                }
            });
        }
    },
    { immediate: true }
);

watch(
    () => props.pabellons,
    (newPabellones) => {
        if (newPabellones && newPabellones.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "pab_id") {
                    field.options = newPabellones.map((pabellon) => ({
                        value: pabellon.value,
                        label: pabellon.text,
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
        props.formFields.forEach((field) => {
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
    <div class="fixed inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-50">
        <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg">
            <div class="border-2 border-[#2EBAA1] p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-[#2EBAA1]">Crear {{ itemName }}</h2>
                <p v-if="successMessage" class="mb-4 text-green-500">{{ successMessage }}</p>
                <div v-for="(field, index) in visibleFields" :key="index">
                    <label :for="field.name" class="block mb-2 text-gray-500">{{ field.label }}:</label>
                    <template v-if="field.type === 'select'">
                        <select :id="field.name" v-model="formData[field.name]" :class="{
                            'text-[#2EBAA1]': formData[field.name] === '',
                            'text-gray-900': formData[field.name] !== '',
                        }" class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50">
                            <option value="" disabled selected>
                                {{ field.name === 'sed_id' ? 'Seleccione una sede' : 'Seleccione un pabellón' }}
                            </option>
                            <option v-for="option in field.options" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                    </template>
                    <template v-else>
                        <input :id="field.name" :type="field.type" v-model="formData[field.name]"
                            :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                            class="flex-grow w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50" />
                    </template>

                    <p v-if="errores[field.name]" class="text-sm text-red-500">
                        {{ errores[field.name][0] }}
                    </p>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <ButtonCrearActualizar @click="submitForm" :loading="loading" :itemName="'Crear'" />
                    <ButtonCerrar @click="cerrarModal" />
                </div>
            </div>
        </div>
    </div>
</template>
