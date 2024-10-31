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
const selectedImageUrl = ref("");

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

const imageUrl = computed(() => {
    return props.item?.tic_archivo
        ? `/storage/${props.item.tic_archivo}`
        : null;
});
</script>

<template>
    <div
        v-if="mostrarModalDetalles"
        class="fixed inset-0 flex items-center justify-center bg-gray-400 bg-opacity-30"
    >
        <div class="w-full max-w-lg p-2 bg-white rounded-lg shadow-lg">
            <div class="p-4 border-2 border-gray-400 rounded-lg">
                <h2 class="mb-4 text-xl font-bold text-gray-600">
                    Detalles {{ itemName }}
                </h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th
                                class="py-2 pr-10 text-left text-gray-600 border-b-2 border-gray-400"
                            >
                                Campo
                            </th>
                            <th
                                class="py-2 text-left text-gray-600 border-b-2 border-gray-400"
                            >
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(field, index) in visibleFields"
                            :key="index"
                            class="border-b border-gray-400"
                        >
                            <td
                                class="py-2 pr-10 font-semibold text-left text-gray-500"
                            >
                                {{ field.label }}
                            </td>
                            <td
                                v-if="field.type === 'file'"
                                class="py-2 text-left text-gray-600"
                            >
                                <div v-if="imageUrl">
                                    <img
                                        :src="imageUrl"
                                        :alt="field.label"
                                        class="object-cover w-32 h-32 border border-gray-300 rounded-lg cursor-pointer"
                                        @click="openImageModal(imageUrl)"
                                    />
                                </div>
                                <div v-else>No disponible</div>
                            </td>
                            <td v-else class="py-2 text-left text-gray-600">
                                {{ item[field.name] || "No disponible" }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-end mt-4">
                    <ButtonCerrar @click="cerrarDetallesModal" />
                </div>
            </div>
        </div>

        <div
            v-if="isImageModalOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80"
        >
            <div class="relative max-w-4xl p-4 bg-white rounded-lg shadow-lg">
                <img
                    :src="selectedImageUrl"
                    alt="Imagen ampliada"
                    class="max-w-full max-h-screen"
                />
                <button
                    @click="closeImageModal"
                    class="absolute top-1 right-1 text-white bg-gray-800 rounded-full pr-2 pl-2 focus:outline-none"
                >
                    X
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
option[disabled] {
    color: #2ebaa1;
}
</style>
