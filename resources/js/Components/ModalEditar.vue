<script setup>
import { ref, watch, onMounted } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";
import axios from "axios";

const props = defineProps({
    item: Object,
    itemName: String,
    formFields: Array,
    prioridads: {
        type: Array,
        default: () => [],
    },
    usuarios: {
        type: Array,
        default: () => [],
    },
    sedes: {
        type: Array,
        default: () => [],
    },
    categorias: {
        type: Array,
        default: () => [],
    },
    pabellons: {
        type: Array,
        default: () => [],
    },
    aulas: {
        type: Array,
        default: () => [],
    },
    mostrarModalEditar: Boolean,
    endpoint: String,
});

const emit = defineEmits(["cerrar", "update"]);

const formData = ref({});
const errores = ref({});
const loading = ref(false);
const successMessage = ref("");
const clickInicial = ref(false);

const initializeFormData = (item) => {
    formData.value = {};
    props.formFields.forEach((field) => {
        formData.value[field.name] = item?.[field.name] || "";
    });
    clickInicial.value = true;
};

const updateSelectOptions = (fieldName, options) => {
    const field = props.formFields.find((f) => f.name === fieldName);
    if (field) {
        field.options = options;
    }
};

watch(
    () => props.prioridads,
    (newOptions) => updateSelectOptions("pri_id", newOptions),
    { immediate: true }
);

watch(
    () => props.usuarios,
    (newOptions) => updateSelectOptions("use_id", newOptions),
    { immediate: true }
);

watch(
    () => props.sedes,
    (newOptions) => updateSelectOptions("sed_id", newOptions),
    { immediate: true }
);

watch(
    () => props.categorias,
    (newOptions) => updateSelectOptions("cat_id", newOptions),
    { immediate: true }
);

watch(
    () => props.pabellons,
    (newOptions) => updateSelectOptions("pab_id", newOptions),
    { immediate: true }
);

watch(
    () => props.aulas,
    (newOptions) => updateSelectOptions("aul_id", newOptions),
    { immediate: true }
);

watch(
    () => props.item,
    (newItem) => {
        if (newItem) {
            initializeFormData(newItem);
        }
    },
    { immediate: true }
);

onMounted(() => {
    props.formFields.forEach((field) => {
        if (field.type === "boolean" && clickInicial.value) {
            formData.value[field.name] = !!formData.value[field.name];
        }
    });
    clickInicial.value = false;
});

const editarItem = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = {};
    let isValid = true;
    props.formFields.forEach((field) => {
        const fieldValue = formData.value[field.name];

        if (field.required && !fieldValue) {
            errores.value[field.name] = [
                `El campo ${field.label} es requerido`,
            ];
            isValid = false;
        }

        if (field.label.toLowerCase().includes("teléfono")) {
            if (!fieldValue) {
                errores.value[field.name] = [
                    `El campo ${field.label} es requerido`,
                ];
                isValid = false;
            } else {
                if (fieldValue.startsWith("+51")) {
                    formData.value[field.name] = fieldValue
                        .replace("+51", "")
                        .trim();
                }

                const regex = /^[0-9]+$/;
                if (!regex.test(formData.value[field.name])) {
                    errores.value[field.name] = [
                        `El campo ${field.label} debe contener solo números.`,
                    ];
                    isValid = false;
                }

                if (formData.value[field.name].length !== 9) {
                    errores.value[field.name] = [
                        `El campo ${field.label} debe tener exactamente 9 dígitos.`,
                    ];
                    isValid = false;
                }
            }
        }

        if (field.type === "boolean") {
            formData.value[field.name] = formData.value[field.name] ? 1 : 0;
        }
    });

    if (!isValid) {
        loading.value = false;
        return;
    }
    try {
        const response = await axios.put(
            `${props.endpoint}/${props.item.id}`,
            formData.value,
            {
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            }
        );
        toast.success(`${props.itemName} actualizado correctamente`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-green-500 p-2",
        });
        emit("update", response.data);
        cerrarModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = error.response.data.errors;
        } else {
            console.error("Error:", error);
        }
        toast.error(`Error al actualizar ${props.itemName}`, {
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
        v-if="mostrarModalEditar"
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-gray-400 bg-opacity-30"
    >
        <div
            class="w-full max-w-lg p-4 bg-white rounded-lg shadow-lg md:max-w-2xl"
        >
            <div class="border-2 border-[#2EBAA1] p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-[#2EBAA1]">
                    Editar {{ itemName }}
                </h2>
                <p v-if="successMessage" class="mb-4 text-green-500">
                    {{ successMessage }}
                </p>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div v-for="(field, index) in formFields" :key="index">
                        <label
                            :for="`form-${field.name}`"
                            class="block mb-2 text-gray-500"
                        >
                            {{ field.label }}:
                        </label>

                        <template v-if="field.type === 'select'">
                            <select
                                :id="`form-${field.name}`"
                                v-model="formData[field.name]"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            >
                                <option value="" disabled>
                                    Seleccione su {{ field.label }}
                                </option>
                                <option
                                    v-for="option in field.options"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.text }}
                                </option>
                            </select>

                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>

                        <template
                            v-else-if="
                                field.type === 'text' ||
                                field.label === 'Teléfono'
                            "
                        >
                            <input
                                :id="`form-${field.name}`"
                                :type="
                                    field.type === 'number' &&
                                    field.label !== 'Teléfono'
                                        ? 'number'
                                        : 'text'
                                "
                                v-model="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            />
                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>

                        <template
                            v-else-if="
                                field.type === 'text' || field.type === 'email'
                            "
                        >
                            <input
                                :id="`form-${field.name}`"
                                :type="field.type"
                                v-model="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            />

                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>

                        <template v-else-if="field.type === 'textarea'">
                            <textarea
                                :id="`form-${field.name}`"
                                v-model="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            ></textarea>

                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>

                        <template v-if="field.type === 'boolean'">
                            <div class="flex items-center mb-4">
                                <input
                                    type="checkbox"
                                    :id="`form-${field.name}`"
                                    v-model="formData[field.name]"
                                    class="hidden peer"
                                />
                                <label
                                    :for="`form-${field.name}`"
                                    class="flex items-center justify-center w-10 h-5 transition-colors duration-300 bg-gray-300 rounded-full cursor-pointer"
                                    :class="{
                                        'bg-green-400': formData[field.name],
                                        'bg-red-400': !formData[field.name],
                                    }"
                                >
                                    <span
                                        class="w-4 h-4 transition-transform duration-300 transform bg-white rounded-full shadow-md"
                                        :class="{
                                            'translate-x-[10px]':
                                                formData[field.name],
                                            '-translate-x-[10px]':
                                                !formData[field.name],
                                        }"
                                    ></span>
                                </label>
                                <span
                                    class="ml-2 text-sm font-medium text-gray-700"
                                >
                                    {{
                                        formData[field.name]
                                            ? "Activo"
                                            : "Inactivo"
                                    }}
                                </span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <ButtonCrearActualizar
                        @click="editarItem"
                        :loading="loading"
                        :itemName="'Actualizar'"
                    />
                    <ButtonCerrar @click="cerrarModal" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
option[disabled] {
    color: #2ebaa1;
}
</style>
