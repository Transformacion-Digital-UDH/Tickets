<script setup>
import { ref, watch } from "vue";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";
import axios from "axios";

const props = defineProps({
    item: Object,
    itemName: String,
    formFields: Array,
    sedes: {
        type: Array,
        default: () => [],
    },
    pabellons: {
        type: Array,
        default: () => [],
    },
    mostrarModalEditar: Boolean,
    endpoint: String,
});

const emit = defineEmits(["cerrar", "update"]);

const formData = ref({});
const errores = ref([]);
const loading = ref(false);
const successMessage = ref("");

watch(
    () => props.item,
    (newItem) => {
        if (newItem) {
            formData.value = { ...newItem };
        }
    },
    { immediate: true }
);

const editarItem = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = [];
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
        emit("update", response.data);
        cerrarModal();
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
        v-if="mostrarModalEditar"
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-50"
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
                                :class="{
                                    'text-[#2EBAA1]':
                                        formData[field.name] === '',
                                    'text-gray-900':
                                        formData[field.name] !== '',
                                }"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            >
                                <option value="" disabled selected>
                                    {{
                                        field.name === "sed_id"
                                            ? "Seleccione una sede"
                                            : "Seleccione un pabellón"
                                    }}
                                </option>
                                <option
                                    v-for="option in field.name === 'sed_id'
                                        ? props.sedes
                                        : props.pabellons"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.text }}
                                </option>
                            </select>
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
