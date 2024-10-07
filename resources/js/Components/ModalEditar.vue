<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from "vue";
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
const formDataOriginal = ref({});
const errores = ref({});
const isMobile = ref(false);
const loading = ref(false);
const successMessage = ref("");
const clickInicial = ref(false);
const selectedFileName = ref({});
const selectedFilePreviews = ref({});

const handleResize = () => {
    isMobile.value = window.innerWidth <= 768;
};

onMounted(() => {
    handleResize();
    window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
});

const initializeFormData = (item) => {
    formData.value = {};
    formDataOriginal.value = {};
    props.formFields.forEach((field) => {
        formData.value[field.name] = item?.[field.name] || "";
        formDataOriginal.value[field.name] = item?.[field.name] || "";

        if (field.type === "file" && item?.[field.name]) {
            selectedFileName.value[field.name] = item[field.name];
            selectedFilePreviews.value[field.name] = {
                name: item[field.name],
                url: `/storage/${item[field.name]}`,
            };
        }
    });
};

const updateSelectOptions = (fieldName, options) => {
    const field = props.formFields.find((f) => f.name === fieldName);
    if (field) {
        field.options = options;
    }
};

["prioridads", "usuarios", "sedes", "categorias", "pabellons", "aulas"].forEach(
    (prop) => {
        watch(
            () => props[prop],
            (newOptions) =>
                updateSelectOptions(`${prop.slice(0, -1)}_id`, newOptions),
            { immediate: true }
        );
    }
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

const handleFileChange = (event, fieldName) => {
    const file = event.target.files[0];
    if (file) {
        selectedFileName.value[fieldName] = file.name;
        formData.value[fieldName] = file;

        const previewURL = URL.createObjectURL(file);
        selectedFilePreviews.value[fieldName] = {
            name: file.name,
            url: previewURL,
        };
    }
};

const updateFile = async () => {
    const data = new FormData();
    let fileUpdated = false;
    props.formFields.forEach((field) => {
        if (
            field.type === "file" &&
            formData.value[field.name] instanceof File
        ) {
            data.append(field.name, formData.value[field.name]);
            fileUpdated = true;
        }
    });

    if (!fileUpdated) return;

    try {
        const response = await axios.post(
            `${props.endpoint}/${props.item.id}/upload`,
            data,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            }
        );

        toast.success(`Imagen actualizada correctamente`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-green-500 p-2",
        });

        emit("update", response.data);

        cerrarModal();
    } catch (error) {
        toast.error(`Error al actualizar la imagen`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-red-500 p-2",
        });
    }
};

const editarItem = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = {};
    let isValid = true;

    const data = {};
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

        if (field.label === "Correo") {
            if (!fieldValue) {
                errores.value[field.name] = [
                    `El campo ${field.label} es requerido`,
                ];
                isValid = false;
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(fieldValue)) {
                    errores.value[field.name] = [
                        `El campo ${field.label} debe ser un correo válido.`,
                    ];
                    isValid = false;
                }
            }
        }

        if (field.type !== "file") {
            data[field.name] = formData.value[field.name] || "";
        }
    });

    if (!isValid) {
        loading.value = false;
        return;
    }

    try {
        const response = await axios.put(
            `${props.endpoint}/${props.item.id}`,
            data,
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

const actualizarItemCompleto = async () => {
    let isFileUpdated = false;
    let isFormUpdated = false;

    props.formFields.forEach((field) => {
        if (
            field.type === "file" &&
            formData.value[field.name] instanceof File
        ) {
            isFileUpdated = true;
        }
    });

    props.formFields.forEach((field) => {
        if (
            formDataOriginal.value[field.name] !== formData.value[field.name] &&
            field.type !== "file"
        ) {
            isFormUpdated = true;
        }
    });

    if (isFileUpdated && isFormUpdated) {
        await editarItem();
        await updateFile();
    } else if (isFormUpdated) {
        await editarItem();
    } else if (isFileUpdated) {
        await updateFile();
    } else {
        toast.info(`No se ha detectado ningún cambio`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-blue-500 p-2",
        });
    }
};

const cerrarModal = () => emit("cerrar");
</script>

<template>
    <div
        class="fixed inset-0 flex items-center justify-center transition-opacity bg-gray-400 bg-opacity-30"
    >
        <div
            class="w-full md:h-auto md:max-w-2xl p-6 bg-white rounded-lg shadow-lg overflow-y-auto max-h-[90vh] md:max-h-[100vh]"
        >
            <div class="border-2 border-[#2EBAA1] p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-[#2EBAA1]">
                    Editar {{ itemName }}
                </h2>
                <p v-if="successMessage" class="mb-4 text-green-500">
                    {{ successMessage }}
                </p>

                <div class="grid gap-4">
                    <div
                        v-for="(field, index) in formFields"
                        :key="index"
                        :class="{
                            'md:col-span-1 grid-cols-1':
                                field.type !== 'textarea' &&
                                field.type !== 'file',
                            'md:col-span-2 grid-cols-2':
                                field.type === 'textarea' ||
                                field.type === 'file',
                        }"
                    >
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

                        <template v-else-if="field.type === 'email'">
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

                        <template
                            v-else-if="
                                field.type === 'text' ||
                                field.label.toLowerCase().includes('teléfono')
                            "
                        >
                            <input
                                :id="`form-${field.name}`"
                                type="text"
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

                        <template v-else-if="field.type === 'file'">
                            <div class="file-upload-wrapper">
                                <input
                                    :id="`form-${field.name}`"
                                    :name="field.name"
                                    type="file"
                                    class="hidden"
                                    @change="
                                        handleFileChange($event, field.name)
                                    "
                                />
                                <label
                                    class="block w-full p-2 mb-1 text-center text-white bg-[#2EBAA1] rounded-md cursor-pointer hover:bg-[#28a890]"
                                    :for="`form-${field.name}`"
                                >
                                    Seleccionar {{ field.label }}
                                </label>

                                <div class="mt-2">
                                    <div
                                        v-if="
                                            selectedFileName[field.name] &&
                                            !isMobile
                                        "
                                        class="text-sm text-gray-500"
                                    >
                                        Archivo seleccionado:
                                        {{ selectedFileName[field.name] }}
                                    </div>

                                    <div
                                        v-if="selectedFilePreviews[field.name]"
                                        class="mt-4"
                                    >
                                        <div
                                            v-for="(file, index) in [
                                                selectedFilePreviews[
                                                    field.name
                                                ],
                                            ]"
                                            :key="index"
                                            class="overflow-hidden border border-gray-300 rounded-lg"
                                        >
                                            <img
                                                :src="file.url"
                                                :alt="file.name"
                                                class="object-cover w-full h-50"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <span
                                    v-if="errores[field.name]"
                                    class="text-red-500 text-sm"
                                >
                                    {{ errores[field.name][0] }}
                                </span>
                            </div>
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
                        @click="actualizarItemCompleto"
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
