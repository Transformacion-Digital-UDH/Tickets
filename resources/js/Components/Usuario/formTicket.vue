<script setup>
import { ref, onMounted } from "vue";
import ButtonCrearActualizar from "@/Components/ButtonCrearActualizar.vue";

const emit = defineEmits(["crear", "cerrar"]);

const propsPadre = defineProps({
    formFields: {
        type: Array,
        default: () => [],
    },
    endpoint: {
        type: String,
        required: true,
    },
    itemName: String,
});

const loading = ref(false);
const errores = ref([]);
const successMessage = ref("");

const formData = ref({
    tic_titulo: "",
    tic_descripcion: "",
    pri_id: "",
    cat_id: "",
    pab_id: "",
    aul_id: "",
});

const prioridades = ref([]);
const categorias = ref([]);
const pabellones = ref([]);
const aulas = ref([]);

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

const submitForm = async () => {
    loading.value = true;
    successMessage.value = "";
    errores.value = [];
    try {
        const response = await axios.post(propsPadre.endpoint, formData.value, {
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        });
        successMessage.value = "Creación exitosa!";
        emit("crear", response.data);
        emit("cerrar");
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errores.value = Object.values(error.response.data.errors).flat();
        } else {
            console.error("Error:", error);
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchPrioridades();
    fetchCategorias();
    fetchPabellones();
    fetchAulas();
});
</script>

<template>
    <div class="p-6">
        <h1 class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl">
            Crear Ticket
        </h1>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md p-6">
            <h2
                class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xm"
            >
                Rellena el formulario para crear un nuevo ticket
            </h2>
            <div>
                <h3>Título</h3>
                <input
                    v-model="formData.tic_titulo"
                    type="text"
                    placeholder="Escribe el título"
                    class="w-full p-2 mb-1 border border-gray-300 rounded-md"
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3>Prioridad</h3>
                    <select
                        v-model="formData.pri_id"
                        class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                    >
                        <option value="" disabled>
                            Seleccione la prioridad
                        </option>
                        <option
                            v-for="prioridad in prioridades"
                            :key="prioridad.value"
                            :value="prioridad.value"
                        >
                            {{ prioridad.text }}
                        </option>
                    </select>
                </div>
                <div>
                    <h3>Categoría</h3>
                    <select
                        v-model="formData.cat_id"
                        class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                    >
                        <option value="" disabled>
                            Seleccione la categoría
                        </option>
                        <option
                            v-for="categoria in categorias"
                            :key="categoria.value"
                            :value="categoria.value"
                        >
                            {{ categoria.text }}
                        </option>
                    </select>
                </div>
                <div>
                    <h3>Pabellón</h3>
                    <select
                        v-model="formData.pab_id"
                        class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                    >
                        <option value="" disabled>
                            Seleccione el pabellón
                        </option>
                        <option
                            v-for="pabellon in pabellones"
                            :key="pabellon.value"
                            :value="pabellon.value"
                        >
                            {{ pabellon.text }}
                        </option>
                    </select>
                </div>
                <div>
                    <h3>Aula</h3>
                    <select
                        v-model="formData.aul_id"
                        class="w-full p-2 mb-1 placeholder-[#2EBAA1] border border-[#2EBAA1] rounded-md focus:border-[#2EBAA1] focus:ring focus:ring-[#2EBAA1] focus:ring-opacity-50"
                    >
                        <option value="" disabled>Seleccione la aula</option>
                        <option
                            v-for="aula in aulas"
                            :key="aula.value"
                            :value="aula.value"
                        >
                            {{ aula.text }}
                        </option>
                    </select>
                </div>
            </div>
            <div>
                <h3>Descripción</h3>
                <textarea
                    v-model="formData.tic_descripcion"
                    placeholder="Escribe la descripción"
                    class="w-full p-2 mb-1 border border-gray-300 rounded-md"
                ></textarea>
            </div>
            <!-- Mostrar errores -->
            <div v-if="errores.length > 0" class="text-red-500">
                <ul>
                    <li v-for="(error, index) in errores" :key="index">
                        {{ error }}
                    </li>
                </ul>
            </div>
            <div class="flex justify-end mt-6 space-x-4">
                <ButtonCrearActualizar
                    @click="submitForm"
                    :loading="loading"
                    :itemName="'Crear'"
                />
            </div>
        </div>
    </div>
</template>
