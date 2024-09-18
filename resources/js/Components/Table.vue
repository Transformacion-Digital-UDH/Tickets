<script>
import CardMobile from "./CardMobile.vue";

export default {
    components: {
        CardMobile,
    },
    props: {
        headers: {
            type: Array,
            required: true,
        },
        items: {
            type: Array,
            required: true,
        },
        entityType: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            isMobile: true,
        };
    },
    mounted() {
        this.handleResize();
        window.addEventListener("resize", this.handleResize);
    },
    beforeUnmount() {
        window.removeEventListener("resize", this.handleResize);
    },
    methods: {
        handleResize() {
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(() => {
                this.isMobile = window.innerWidth < 530;
            }, 150);
        },
    },
    computed: {
        sedeKeys() {
            return ["id", "sed_direccion", "sed_telefono"];
        },
        userKeys() {
            return ["id", "celular", "email", "sed_id"];
        },
        categoriaKeys() {
            return ["id"];
        },
        pabellonKeys() {
            return ["id", "sed_id"];
        },
        aulaKeys() {
            return ["id", "pab_id"];
        },
        getStatusKey() {
            if (this.items.length > 0) {
                const statusKeys = [
                    "sed_activo",
                    "activo",
                    "cat_activo",
                    "aul_activo",
                    "pab_activo",
                ];
                return Object.keys(this.items[0]).find((key) =>
                    statusKeys.includes(key)
                );
            }
            return null;
        },
        excludedKeys() {
            let keysToExclude = [];

            switch (this.entityType) {
                case "sede":
                    keysToExclude = [...this.sedeKeys];
                    break;
                case "user":
                    keysToExclude = [...this.userKeys];
                    break;
                case "categoria":
                    keysToExclude = [...this.categoriaKeys];
                    break;
                case "pabellon":
                    keysToExclude = [...this.pabellonKeys];
                    break;
                case "aula":
                    keysToExclude = [...this.aulaKeys];
                    break;
                default:
                    keysToExclude = [];
            }

            if (this.getStatusKey) {
                keysToExclude.push(this.getStatusKey);
            }

            return keysToExclude;
        },
        filteredItemKeys() {
            return (item) => {
                return Object.keys(item).filter(
                    (key) => !this.excludedKeys.includes(key)
                );
            };
        },
    },
};
</script>

<template>
    <div>
        <CardMobile
            v-if="isMobile"
            :items="items"
            :entityType="entityType"
            @view="$emit('view', $event)"
            @edit="$emit('edit', $event)"
            @eliminar="$emit('eliminar', $event)"
        />

        <div v-else class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-white">
                    <tr>
                        <th
                            v-for="header in headers"
                            :key="header"
                            class="px-2 py-2 text-xs font-bold text-left text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                        >
                            {{ header }}
                        </th>
                        <th
                            class="px-2 py-2 text-xs font-bold text-center text-gray-500 uppercase sm:px-4 sm:py-3 sm:text-sm md:text-base"
                        >
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in items"
                        :key="item.id"
                        class="transition-colors duration-200 border-b hover:bg-gray-100"
                    >
                        <td
                            class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                        >
                            {{ index + 1 }}
                        </td>
                        <td
                            v-for="key in filteredItemKeys(item)"
                            :key="key"
                            class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                        >
                            {{ item[key] }}
                        </td>
                        <td
                            class="px-2 py-2 text-xs text-gray-400 sm:px-4 sm:py-3 sm:text-sm md:text-base"
                        >
                            <span
                                :class="{
                                    'bg-green-100 text-green-800':
                                        item[getStatusKey] === 1,
                                    'bg-red-100 text-red-800':
                                        item[getStatusKey] === 0,
                                }"
                                class="px-2 py-1 text-xs font-semibold rounded-full sm:text-xs md:text-sm"
                            >
                                {{
                                    item[getStatusKey] === 1
                                        ? "Activo"
                                        : "Inactivo"
                                }}
                            </span>
                        </td>
                        <td
                            class="flex flex-col items-center justify-center py-2 space-y-2 sm:py-3 sm:flex-row sm:space-x-3 sm:space-y-0"
                        >
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
                        </td>
                    </tr>
                    <tr v-if="items.length === 0">
                        <td
                            colspan="7"
                            class="px-4 py-3 text-xs text-center text-gray-500 sm:text-sm"
                        >
                            No se encontraron resultados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
