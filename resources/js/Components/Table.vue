<script>
export default {
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
    computed: {
        sedeKeys() {
            return ["id", "sed_direccion", "sed_telefono"];
        },
        userKeys() {
            return ["id", "celular", "sed_id", "sed_nombre"];
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
                const statusKeys = ["sed_activo", "activo", "cat_activo", "aul_activo", "pab_activo"];
                return Object.keys(this.items[0]).find((key) =>
                    statusKeys.includes(key)
                );
            }
            return null;
        },
        excludedKeys() {
            let keysToExclude = [];

            switch (this.entityType) {
                case 'sede':
                    keysToExclude = [...this.sedeKeys];
                    break;
                case 'user':
                    keysToExclude = [...this.userKeys];
                    break;
                case 'categoria':
                    keysToExclude = [...this.categoriaKeys];
                    break;
                case 'pabellon':
                    keysToExclude = [...this.pabellonKeys];
                    break;
                case 'aula':
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
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-white">
                <tr>
                    <th
                        v-for="header in headers"
                        :key="header"
                        class="px-4 py-3 text-[14px] font-bold text-left text-gray-500 uppercase"
                    >
                        {{ header }}
                    </th>
                    <th
                        class="px-4 py-3 text-[14px] font-bold text-center text-gray-500 uppercase"
                    >
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(item, index) in items"
                    :key="item.id"
                    class="transition-colors duration-200 border-b"
                >
                    <td class="px-4 py-3 text-[14px] text-gray-400">
                        {{ index + 1 }}
                    </td>
                    <td
                        v-for="key in filteredItemKeys(item)"
                        :key="key"
                        class="px-4 py-3 text-[14px] text-gray-400"
                    >
                        {{ item[key] }}
                    </td>
                    <td class="px-4 py-3 text-[14px] text-gray-400">
                        <span
                            :class="{
                                'bg-green-100 text-green-800': item[getStatusKey] === 1,
                                'bg-red-100 text-red-800': item[getStatusKey] === 0,
                            }"
                            class="px-2 py-1 text-xs font-semibold rounded-full"
                        >
                            {{ item[getStatusKey] === 1 ? "Activo" : "Inactivo" }}
                        </span>
                    </td>
                    <td class="flex items-center justify-center py-3 space-x-3">
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
                        class="px-4 py-3 text-[11px] text-center text-gray-500"
                    >
                        No se encontraron resultados.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
