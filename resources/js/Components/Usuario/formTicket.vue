<script setup>
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import confetti from "canvas-confetti";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";
import ModalConfirm from "@/Components/ModalConfirm.vue";
import axios from "axios";

const emit = defineEmits(["crear", "cerrar"]);

const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
    itemName: String,
});

const messages = [
    "Completa el formulario.",
    "Agrega correctamente los detalles.",
    "Revisa los campos antes de enviar.",
];
const currentMessage = ref("");
let messageIndex = 0;
let charIndex = 0;
let isDeleting = false;
const typingSpeed = 150;
const deletingSpeed = 50;
const pauseBetweenMessages = 2000;
const loading = ref(false);
const errores = ref({});
const successMessage = ref("");
const selectedFileName = ref("");
const selectedFilePreview = ref("");
const formData = ref({
    tic_titulo: "",
    tic_descripcion: "",
    tic_archivo: null,
    pri_id: "",
    cat_id: "",
    pab_id: "",
    aul_id: "",
});

const prioridades = ref([]);
const categorias = ref([]);
const pabellones = ref([]);
const aulas = ref([]);
const showModal = ref(false);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFileName.value = file.name;
        selectedFilePreview.value = URL.createObjectURL(file);
        formData.value.tic_archivo = file;
    } else {
        selectedFileName.value = "";
        selectedFilePreview.value = "";
        formData.value.tic_archivo = null;
    }
};

const typeMessage = () => {
    const current = messages[messageIndex];

    if (isDeleting) {
        currentMessage.value = current.substring(0, charIndex--);
    } else {
        currentMessage.value = current.substring(0, charIndex++);
    }

    if (!isDeleting && charIndex === current.length) {
        setTimeout(() => (isDeleting = true), pauseBetweenMessages);
    } else if (isDeleting && charIndex === 0) {
        isDeleting = false;
        messageIndex = (messageIndex + 1) % messages.length;
    }

    setTimeout(typeMessage, isDeleting ? deletingSpeed : typingSpeed);
};

const resetForm = () => {
    formData.value = {
        tic_titulo: "",
        tic_descripcion: "",
        tic_archivo: null,
        pri_id: "",
        cat_id: "",
        pab_id: "",
        aul_id: "",
    };
    errores.value = {};
    selectedFileName.value = "";
    selectedFilePreview.value = "";
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.value = "";
    }
};

const fetchPrioridades = async () => {
    try {
        const response = await axios.get("/prioridades");
        prioridades.value = response.data
            .filter((prioridad) => prioridad.pri_activo)
            .map((prioridad) => ({
                value: prioridad.id,
                text: prioridad.pri_nombre,
            }));
    } catch (error) {
        console.error("Error al cargar las prioridades:", error);
    }
};

const fetchCategorias = async () => {
    try {
        const response = await axios.get("/categorias");
        categorias.value = response.data
            .filter((categoria) => categoria.cat_activo)
            .map((categoria) => ({
                value: categoria.id,
                text: categoria.cat_nombre,
            }));
    } catch (error) {
        console.error("Error al cargar las categorías:", error);
    }
};

const fetchPabellones = async () => {
    try {
        const response = await axios.get("/pabellones");
        pabellones.value = response.data
            .filter((pabellon) => pabellon.pab_activo)
            .map((pabellon) => ({
                value: pabellon.id,
                text: pabellon.pab_nombre,
            }));
    } catch (error) {
        console.error("Error al cargar los pabellones:", error);
    }
};

const fetchAulas = async () => {
    try {
        const response = await axios.get("/aulas");
        aulas.value = response.data
            .filter((aula) => aula.aul_activo)
            .map((aula) => ({
                value: aula.id,
                text: aula.aul_numero,
            }));
    } catch (error) {
        console.error("Error al cargar las aulas:", error);
    }
};

const submitForm = () => {
    showModal.value = true;
};

const handleConfirm = async () => {
    showModal.value = false;
    loading.value = true;
    successMessage.value = "";
    errores.value = {};

    const data = new FormData();

    Object.keys(formData.value).forEach((key) => {
        if (key === "tic_archivo" && formData.value[key]) {
            data.append(key, formData.value[key]);
        } else {
            data.append(key, formData.value[key] || "");
        }
    });

    if (!formData.value.tic_titulo) {
        errores.value.tic_titulo = "El título es obligatorio.";
    }
    if (!formData.value.tic_descripcion) {
        errores.value.tic_descripcion = "La descripción es obligatoria.";
    }
    if (!formData.value.pri_id) {
        errores.value.pri_id = "La prioridad es obligatoria.";
    }
    if (!formData.value.cat_id) {
        errores.value.cat_id = "La categoría es obligatoria.";
    }
    if (!formData.value.pab_id) {
        errores.value.pab_id = "El pabellón es obligatorio.";
    }
    if (!formData.value.aul_id) {
        errores.value.aul_id = "El aula es obligatoria.";
    }

    if (Object.keys(errores.value).length > 0) {
        loading.value = false;
        return;
    }

    try {
        const response = await axios.post(props.endpoint, data, {
            headers: {
                "Content-Type": "multipart/form-data",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        successMessage.value = "Creación exitosa!";
        alertaCreacion();
        triggerConfetti();
        resetForm();
        emit("crear", response.data);
        emit("cerrar");
    } catch (error) {
        toast.error("Hubo un error al crear su ticket", {
            autoClose: 3000,
            position: "bottom-right",
            style: {
                width: "400px",
            },
            className: "border-l-4 border-red-500 p-2",
        });
    } finally {
        loading.value = false;
    }
};

const handleCancel = () => {
    showModal.value = false;
};

const triggerConfetti = () => {
    const end = Date.now() + 2 * 1000;

    const width = window.innerWidth;
    const height = window.innerHeight;

    (function frame() {
        confetti({
            particleCount: 5,
            angle: 60,
            spread: 55,
            startVelocity: 30,
            decay: 0.9,
            scalar: 1,
            shapes: ["circle", "square"],
            colors: ["#ff0000", "#00ff00", "#0000ff"],
            origin: {
                x: Math.random(),
                y: Math.random(),
            },
            position: {
                x: width / 2,
                y: height / 2,
            },
        });
        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
};

const alertaCreacion = () => {
    toast.success("Su ticket se ha creado correctamente", {
        autoClose: 3000,
        position: "bottom-right",
        style: {
            width: "400px",
        },
        className: "border-l-4 border-green-500 p-2",
    });
};

onMounted(() => {
    typeMessage();
    fetchPrioridades();
    fetchCategorias();
    fetchPabellones();
    fetchAulas();
});
</script>

<template>
    <div class="p-4">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Formulario para crear Tickets
        </h1>
        <div class="overflow-x-auto rounded-lg shadow-custom p-3 pt-5">
            <h2
                class="dynamic-text mb-3 text-xs font-bold text-gray-500 sm:text-sm md:text-base lg:text-lg xl:text-xl"
            >
                {{ currentMessage }}
            </h2>
            <div class="mb-3">
                <h3 class="block mb-1 text-gray-500 flex">
                    Título
                    <p class="text-red-600">*</p>
                </h3>
                <input
                    v-model="formData.tic_titulo"
                    type="text"
                    placeholder="Escribe el título..."
                    class="w-full p-2 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                />
                <span class="text-red-500 text-sm">{{
                    errores.tic_titulo
                }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h3 class="block mb-1 text-gray-500 flex">
                        Prioridad
                        <p class="text-red-600">*</p>
                    </h3>
                    <select
                        v-model="formData.pri_id"
                        class="w-full p-2 border border-[#2EBAA1] rounded-md"
                    >
                        <option value="" disabled selected>
                            Seleccione su prioridad
                        </option>
                        <option
                            v-for="prioridad in prioridades"
                            :key="prioridad.value"
                            :value="prioridad.value"
                        >
                            {{ prioridad.text }}
                        </option>
                    </select>
                    <span class="text-red-500 text-sm">{{
                        errores.pri_id
                    }}</span>
                </div>
                <div>
                    <h3 class="block mb-1 text-gray-500 flex">
                        Categoría
                        <p class="text-red-600">*</p>
                    </h3>
                    <select
                        v-model="formData.cat_id"
                        class="w-full p-2 border border-[#2EBAA1] rounded-md"
                    >
                        <option value="" disabled selected>
                            Seleccione su categoría
                        </option>
                        <option
                            v-for="categoria in categorias"
                            :key="categoria.value"
                            :value="categoria.value"
                        >
                            {{ categoria.text }}
                        </option>
                    </select>
                    <span class="text-red-500 text-sm">{{
                        errores.cat_id
                    }}</span>
                </div>
                <div>
                    <h3 class="block mb-1 text-gray-500 flex">
                        Pabellón
                        <p class="text-red-600">*</p>
                    </h3>
                    <select
                        v-model="formData.pab_id"
                        class="w-full p-2 border border-[#2EBAA1] rounded-md"
                    >
                        <option value="" disabled selected>
                            Seleccione su pabellón
                        </option>
                        <option
                            v-for="pabellon in pabellones"
                            :key="pabellon.value"
                            :value="pabellon.value"
                        >
                            {{ pabellon.text }}
                        </option>
                    </select>
                    <span class="text-red-500 text-sm">{{
                        errores.pab_id
                    }}</span>
                </div>
                <div>
                    <h3 class="block mb-1 text-gray-500 flex">
                        Aula
                        <p class="text-red-600">*</p>
                    </h3>
                    <select
                        v-model="formData.aul_id"
                        class="w-full p-2 border border-[#2EBAA1] rounded-md"
                    >
                        <option value="" disabled selected>
                            Seleccione su aula
                        </option>
                        <option
                            v-for="aula in aulas"
                            :key="aula.value"
                            :value="aula.value"
                        >
                            {{ aula.text }}
                        </option>
                    </select>
                    <span class="text-red-500 text-sm">{{
                        errores.aul_id
                    }}</span>
                </div>
            </div>
            <div>
                <h3 class="block mb-1 mt-4 text-gray-500 flex">
                    Descripción
                    <p class="text-red-600">*</p>
                </h3>
                <textarea
                    v-model="formData.tic_descripcion"
                    rows="4"
                    placeholder="Escribe la descripción..."
                    class="w-full p-2 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                ></textarea>
                <span class="text-red-500 text-sm">{{
                    errores.tic_descripcion
                }}</span>
            </div>
            <div class="file-upload-wrapper">
                <h3 class="block mb-1 mt-2 text-gray-500 flex">Imagen</h3>
                <label
                    class="block w-full p-2 mb-1 text-center text-white bg-[#2EBAA1] rounded-md cursor-pointer hover:bg-[#28a890]"
                >
                    Seleccionar archivo
                    <input
                        type="file"
                        @change="handleFileChange"
                        class="hidden"
                    />
                </label>
                <!-- Botón para tomar foto -->
                <label
                    class="block w-full p-2 mb-1 text-center text-white bg-[#2EBAA1] rounded-md cursor-pointer hover:bg-[#28a890]"
                >
                    <input
                        type="file"
                        capture="environment"
                        class="hidden"
                        @change="handleFileChange"
                    />
                    Tomar Foto
                </label>
                <div class="mt-2">
                    <div v-if="selectedFileName" class="text-sm text-gray-500">
                        Archivo seleccionado: {{ selectedFileName }}
                    </div>

                    <div v-if="selectedFilePreview" class="mt-4">
                        <img
                            :src="selectedFilePreview"
                            alt="Vista previa del archivo"
                            class="object-cover w-full h-50"
                        />
                    </div>
                </div>
                <span
                    v-if="errores['tic_archivo']"
                    class="text-red-500 text-sm"
                >
                    {{ errores["tic_archivo"] }}
                </span>
            </div>
            <div class="flex justify-end mt-6 space-x-4">
                <ButtonCrearActualizar
                    @click="submitForm"
                    :loading="loading"
                    :itemName="'Crear'"
                />
            </div>
        </div>
        <ModalConfirm
            :visible="showModal"
            @confirm="handleConfirm"
            @cancel="handleCancel"
        />
    </div>
</template>

<style scoped>
.dynamic-text {
    border-right: 2px solid #2ebaa1;
    white-space: nowrap;
    overflow: hidden;
    display: inline-block;
    animation: blink 1s steps(1) infinite;
    line-height: 1.1;
    height: 1.1em;
    max-width: 100%;
}

@keyframes blink {
    0%,
    100% {
        border-color: transparent;
    }
    50% {
        border-color: #2ebaa1;
    }
}

.shadow-custom {
    box-shadow: 0 5px 8px rgba(0, 0, 0, 0.5);
}

option[disabled] {
    color: #2ebaa1;
}
</style>
