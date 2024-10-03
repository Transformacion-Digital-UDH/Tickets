<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    estadoFilters: Array,
});

const emit = defineEmits(["update-filters"]);

const showEstadoDropdown = ref(false);

const toggleEstadoDropdown = (event) => {
    showEstadoDropdown.value = !showEstadoDropdown.value;
    event.stopPropagation();
};

const handleCheckboxChange = (filterValue) => {
    let updatedFilters = [...props.estadoFilters];

    if (updatedFilters.includes(filterValue)) {
        updatedFilters = updatedFilters.filter(
            (value) => value !== filterValue
        );
    } else {
        updatedFilters.push(filterValue);
    }

    emit("update-filters", updatedFilters);
};

const closeDropdown = (event) => {
    const dropdown = document.querySelector(".estado-dropdown");
    if (dropdown && !dropdown.contains(event.target)) {
        showEstadoDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", closeDropdown);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", closeDropdown);
});
</script>

<template>
    <div class="relative text-right mb-2 mr-1">
        <i
            class="fas fa-sliders-h cursor-pointer text-gray-500"
            @click="toggleEstadoDropdown"
            title="Filtrar por estado"
        ></i>

        <div
            v-if="showEstadoDropdown"
            class="estado-dropdown absolute right-0 z-10 mt-2 w-48 bg-white border border-gray-300 shadow-md rounded-md p-2"
        >
            <div class="grid grid-cols-1 gap-2">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('Abierto')"
                        @change="handleCheckboxChange('Abierto')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">Abierto</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('Asignado')"
                        @change="handleCheckboxChange('Asignado')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">Asignado</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('En progreso')"
                        @change="handleCheckboxChange('En progreso')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">En progreso</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('Resuelto')"
                        @change="handleCheckboxChange('Resuelto')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">Resuelto</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('Cerrado')"
                        @change="handleCheckboxChange('Cerrado')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">Cerrado</span>
                </label>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        :checked="props.estadoFilters.includes('Reabierto')"
                        @change="handleCheckboxChange('Reabierto')"
                        class="form-checkbox h-5 w-5 text-[#2EBAA1] focus:ring-[#2EBAA1] checked:border-[#2EBAA1]"
                    />
                    <span class="ml-2 text-gray-700 text-sm">Reabierto</span>
                </label>
            </div>
        </div>
    </div>
</template>
