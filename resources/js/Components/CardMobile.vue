<script>
export default {
    props: {
        items: {
            type: Array,
            required: true,
        },
        entityType: {
            type: String,
            required: true,
        },
    },
    methods: {
        getItemDisplayKeys(item) {
            switch (this.entityType) {
                case "sede":
                    return {
                        name: item.sed_nombre,
                        secondaryField: item.sed_ciudad,
                        status: item.sed_activo ? "Activo" : "Inactivo",
                    };
                case "user":
                    return {
                        name: item.name,
                        secondaryField: item.sed_nombre,
                        status: item.activo ? "Activo" : "Inactivo",
                    };
                case "categoria":
                    return {
                        name: item.cat_nombre,
                        secondaryField: "",
                        status: item.cat_activo ? "Activo" : "Inactivo",
                    };
                case "aula":
                    return {
                        name: item.aul_numero,
                        secondaryField: item.pab_nombre,
                        status: item.aul_activo ? "Activo" : "Inactivo",
                    };
                case "pabellon":
                    return {
                        name: item.pab_nombre,
                        secondaryField: item.sed_nombre,
                        status: item.pab_activo ? "Activo" : "Inactivo",
                    };
                default:
                    return {
                        name: "Desconocido",
                        secondaryField: "N/A",
                        status: "N/A",
                    };
            }
        },
    },
};
</script>

<template>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
            v-if="items.length > 0"
            v-for="(item, index) in items"
            :key="item.id"
            class="relative w-full"
        >
            <span
                class="absolute top-0 left-0 w-full h-full mt-1 ml-1 rounded-lg bg-green-400"
            ></span>
            <div
                class="relative h-full p-6 bg-white border-2 rounded-lg border-green-400 shadow-lg"
            >
                <div
                    class="absolute px-3 py-1 text-xs font-bold text-white uppercase bg-green-500 rounded-full top-1 right-1"
                >
                    {{ item.row_number }}
                </div>
                <h2
                    class="text-lg font-bold text-gray-800 uppercase truncate mt-2"
                >
                    {{ getItemDisplayKeys(item).name }}
                </h2>
                <p
                    class="text-gray-600"
                    v-if="getItemDisplayKeys(item).secondaryField"
                >
                    {{ getItemDisplayKeys(item).secondaryField }}
                </p>
                <p class="text-gray-600">
                    Estado:
                    <span
                        :class="
                            getItemDisplayKeys(item).status === 'Activo'
                                ? 'text-green-600'
                                : 'text-red-600'
                        "
                    >
                        {{ getItemDisplayKeys(item).status }}
                    </span>
                </p>
                <div class="flex mt-4 space-x-2">
                    <button
                        @click="$emit('view', item)"
                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-blue-300 to-blue-500 hover:from-blue-400 hover:to-blue-600"
                        title="Ver detalles"
                    >
                        <i class="fas fa-eye"></i>
                    </button>
                    <button
                        @click="$emit('edit', item)"
                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-teal-300 to-teal-500 hover:from-teal-400 hover:to-green-600"
                        title="Editar"
                    >
                        <i class="fas fa-edit"></i>
                    </button>
                    <button
                        @click="$emit('eliminar', item)"
                        class="text-transparent transition-all duration-300 bg-clip-text bg-gradient-to-r from-red-300 to-red-500 hover:from-red-400 hover:to-red-600"
                        title="Eliminar"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div
            v-else
            colspan="7"
            class="py-3 text-xs text-left text-gray-500 sm:text-sm"
        >
            <p>No se encontraron resultados.</p>
        </div>
    </div>
</template>
