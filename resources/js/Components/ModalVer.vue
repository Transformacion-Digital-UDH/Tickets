<script setup>
import { ref, watch, computed } from "vue";
import ButtonCerrar from "@/Components/ButtonCerrar.vue";

const props = defineProps({
    item: Object,
    itemName: String,
    formFieldsVer: Array,
    mostrarModalDetalles: Boolean,
});

const formData = ref({});
const isImageModalOpen = ref(false);
const isTextModalOpen = ref(false);
const selectedImageUrl = ref("");
const selectedFieldText = ref("");

const visibleFields = computed(() => {
    return props.formFieldsVer.filter((field) => field.type !== "boolean");
});

const initializeFormData = (item) => {
    formData.value = {};
    props.formFieldsVer.forEach((field) => {
        formData.value[field.name] = item?.[field.name] || "";
    });
};

watch(
    () => props.item,
    (newItem) => {
        if (newItem) {
            formData.value = { ...newItem };
            initializeFormData(newItem);
        }
    },
    { immediate: true }
);

const emit = defineEmits(["close"]);

const cerrarDetallesModal = () => {
    emit("close");
};

const openImageModal = (imageUrl) => {
    selectedImageUrl.value = imageUrl;
    isImageModalOpen.value = true;
};

const closeImageModal = () => {
    isImageModalOpen.value = false;
    selectedImageUrl.value = "";
};

const openTextModal = (text) => {
    selectedFieldText.value = text;
    isTextModalOpen.value = true;
};

const closeTextModal = () => {
    isTextModalOpen.value = false;
    selectedFieldText.value = "";
};

const imageUrl = computed(() => {
    const fileField = props.formFieldsVer.find(
        (field) => field.type === "file" && props.item[field.name]
    );
    return fileField ? `/storage/${props.item[fileField.name]}` : null;
});
</script>

<template>
    <div v-if="mostrarModalDetalles" class="fixed inset-0 flex items-center justify-center bg-gray-400 bg-opacity-30">
        <div
            class="w-full md:h-auto md:max-w-2xl p-6 bg-white rounded-lg shadow-lg overflow-y-auto max-h-[90vh] md:max-h-[100vh]">
            <div class="p-4 border-2 border-gray-400 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-gray-600">
                    Detalles {{ itemName }}
                </h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 pr-10 text-left text-gray-600 border-b-2 border-gray-400">
                                Campo
                            </th>
                            <th class="py-2 text-left text-gray-600 border-b-2 border-gray-400">
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(field, index) in visibleFields" :key="index" class="border-b border-gray-400">
                            <td class="py-2 pr-10 font-semibold text-left text-gray-500">
                                {{ field.label }}
                            </td>
                            <td v-if="field.type === 'file'" class="py-2 text-left text-gray-600">
                                <div v-if="imageUrl">
                                    <img :src="imageUrl" :alt="field.label"
                                        class="object-cover w-32 h-32 border border-gray-300 rounded-lg cursor-pointer"
                                        @click="openImageModal(imageUrl)" />
                                </div>
                                <div v-else>No disponible</div>
                            </td>
                            <td v-else class="py-2 text-left text-gray-600">
                                <div class="truncate-custom cursor-pointer" :title="item[field.name] || 'No disponible'"
                                    @click="openTextModal(item[field.name] || 'No disponible')">
                                    {{ item[field.name] || "No disponible" }}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-end mt-4">
                    <ButtonCerrar @click="cerrarDetallesModal" />
                </div>
            </div>
        </div>

        <!-- Modal para la imagen -->
        <div v-if="isImageModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80">
            <div class="relative max-w-4xl p-4 bg-white rounded-lg shadow-lg">
                <img :src="selectedImageUrl" alt="Imagen ampliada" class="max-w-full max-h-screen" />
                <button @click="closeImageModal"
                    class="absolute top-1 right-1 text-white bg-gray-800 rounded-full pr-2 pl-2 focus:outline-none">
                    X
                </button>
            </div>
        </div>

        <!-- Modal para texto completo -->
        <div v-if="isTextModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 p-4">
            <div class="relative max-w-lg w-full bg-white p-6 rounded-lg shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-700">Texto completo</h3>
                <p class="text-gray-600 break-words overflow-y-auto max-h-[50vh]">
                    {{ selectedFieldText }}
                </p>
                <div class="flex justify-end mt-4">
                    <button @click="closeTextModal"
                        class="px-4 py-2 text-white bg-[#2EBAA1] rounded-md hover:bg-[#28a890] focus:outline-none">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
option[disabled] {
    color: #2ebaa1;
}

@media (max-width: 660px) {
    .truncate-custom {
        max-width: 180px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
}
</style>
