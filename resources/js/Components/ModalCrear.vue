<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";

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
    prioridads: {
        type: Array,
        default: () => [],
    },
    usuarios: {
        type: Array,
        default: () => [],
    },
    categorias: {
        type: Array,
        default: () => [],
    },
    sedes: {
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
});

const emit = defineEmits(["cerrar", "crear"]);

const formData = ref({});
const errores = ref([]);
const loading = ref(false);
const successMessage = ref("");

const visibleFields = computed(() => {
    return props.formFields.filter((field) => field.type !== "boolean");
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
    () => props.prioridads,
    (newPrioridades) => {
        if (newPrioridades && newPrioridades.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "pri_id") {
                    field.options = newPrioridades.map((prioridad) => ({
                        value: prioridad.value,
                        label: prioridad.text,
                    }));
                }
            });
        }
    },
    { immediate: true }
);

watch(
    () => props.usuarios,
    (newUsuarios) => {
        if (newUsuarios && newUsuarios.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "use_id") {
                    field.options = newUsuarios.map((usuario) => ({
                        value: usuario.value,
                        label: usuario.text,
                    }));
                }
            });
        }
    },
    { immediate: true }
);

watch(
    () => props.categorias,
    (newCategorias) => {
        if (newCategorias && newCategorias.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "cat_id") {
                    field.options = newCategorias.map((categoria) => ({
                        value: categoria.value,
                        label: categoria.text,
                    }));
                }
            });
        }
    },
    { immediate: true }
);

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

watch(
    () => props.aulas,
    (newAulas) => {
        if (newAulas && newAulas.length > 0) {
            props.formFields.forEach((field) => {
                if (field.name === "aul_id") {
                    field.options = newAulas.map((aula) => ({
                        value: aula.value,
                        label: aula.text,
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
                const phoneValue = String(fieldValue);

                if (phoneValue.startsWith("+51")) {
                    formData.value[field.name] = phoneValue
                        .replace("+51", "")
                        .trim();
                } else {
                    formData.value[field.name] = phoneValue.trim();
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
    });

    if (!isValid) {
        loading.value = false;
        return;
    }
    try {
        const response = await axios.post(props.endpoint, formData.value, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        toast.success(`${props.itemName} creado correctamente`, {
            autoClose: 3000,
            position: "bottom-right",
            style: { width: "400px" },
            className: "border-l-4 border-green-500 p-2",
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
        toast.error(`Error al crear ${props.itemName}`, {
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
            <div class="border-2 border-[#2EBAA1] p-4 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-[#2EBAA1]">
                    Crear {{ itemName }}
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
                                    'text-[#2EBAA1]':
                                        formData[field.name] === '',
                                    'text-gray-900':
                                        formData[field.name] !== '',
                                }"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
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
                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>
                        <template v-else-if="field.type === 'textarea'">
                            <textarea
                                :id="field.name"
                                :name="field.name"
                                v-model="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                :autocomplete="field.autocomplete || 'off'"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            ></textarea>
                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>
                        <template v-else-if="field.type === 'number'">
                            <input
                                :id="field.name"
                                :name="field.name"
                                :type="field.type"
                                v-model.number="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                :autocomplete="field.autocomplete || 'off'"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            />
                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>
                        <template v-else>
                            <input
                                :id="field.name"
                                :name="field.name"
                                :type="field.type"
                                v-model="formData[field.name]"
                                :placeholder="`Ingrese ${field.label.toLowerCase()}`"
                                :autocomplete="field.autocomplete || 'off'"
                                class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                            />
                            <span
                                v-if="errores[field.name]"
                                class="text-red-500 text-sm"
                            >
                                {{ errores[field.name][0] }}
                            </span>
                        </template>
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <ButtonCrearActualizar
                        @click="submitForm"
                        :loading="loading"
                        :itemName="'Crear'"
                    />
                    <ButtonCerrar @click="cerrarModal" />
                </div>
            </div>
        </div>
    </div>
</template>
