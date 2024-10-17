<script setup>
import { ref, onMounted } from "vue";
import { toast } from "vue3-toastify";
import axios from "axios";
import "vue3-toastify/dist/index.css";
import AppLayout from "@/Layouts/AppLayout.vue";
import Dropdown from "@/Components/Dropdown.vue";

const props = defineProps({
    ticket: Object,
    comentarios: Array,
    success: String,
});

const ticket = ref(props.ticket);
const comentarios = ref([...props.comentarios]);
const nuevoComentario = ref("");
const error = ref(null);

const getInitials = (name) => {
    if (!name) return "";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
};

const subirComentario = async () => {
    if (!ticket.value || !ticket.value.id) {
        error.value = "El ticket no está definido o falta su ID.";
        mostrarError(error.value);
        return;
    }

    if (!nuevoComentario.value.trim()) {
        error.value = "El comentario no puede estar vacío.";
        mostrarAdvertencia(error.value);
        return;
    }

    try {
        error.value = null;

        const response = await axios.post(
            `/tickets/${ticket.value.id}/comentarios`,
            { com_texto: nuevoComentario.value },
            {
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            }
        );

        if (response.status === 201 && response.data.comentario) {
            comentarios.value.push(response.data.comentario);

            nuevoComentario.value = "";

            mostrarExito("Comentario registrado");
        }
    } catch (err) {
        error.value = "Error al enviar el comentario.";
        mostrarError(error.value);
        console.error(
            "Error al enviar el comentario:",
            err.response?.data?.message || err.message
        );
    }
};

const mostrarExito = (mensaje) => {
    toast.success(mensaje, {
        autoClose: 3000,
        position: "bottom-right",
        style: { width: "400px" },
        className: "border-l-4 border-green-500 p-2",
    });
};

const mostrarAdvertencia = (mensaje) => {
    toast.warn(mensaje, {
        autoClose: 3000,
        position: "bottom-right",
        style: { width: "400px" },
    });
};

const mostrarError = (mensaje) => {
    toast.error(mensaje, {
        autoClose: 3000,
        position: "bottom-right",
        style: { width: "400px" },
    });
};

onMounted(() => {
    if (props.success) {
        mostrarExito(props.success);
    }
});
</script>

<template>
    <AppLayout title="Comentarios">
        <div class="p-6">
            <h1
                class="mb-6 text-sm font-bold text-gray-500 sm:text-lg md:text-xl"
            >
                Historial de Comunicaciones
            </h1>

            <div v-if="error" class="text-red-500 mb-4">{{ error }}</div>

            <div class="flex items-start mb-6">
                <div class="flex-shrink-0 mr-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <div
                                class="flex items-center text-sm transition border-2 border-transparent rounded-full focus:outline-none"
                            >
                                <template
                                    v-if="
                                        $page.props.auth.user.profile_photo_url
                                    "
                                >
                                    <img
                                        class="w-12 h-12 rounded-full"
                                        :src="
                                            $page.props.auth.user
                                                .profile_photo_url
                                        "
                                        :alt="$page.props.auth.user.name"
                                    />
                                </template>
                                <template v-else>
                                    <div
                                        class="flex items-center justify-center w-12 h-12 font-bold text-[#2EBAA1] bg-gray-200 rounded-full"
                                    >
                                        {{
                                            getInitials(
                                                $page.props.auth.user.name
                                            )
                                        }}
                                    </div>
                                </template>
                            </div>
                        </template>
                    </Dropdown>
                </div>

                <div class="flex-grow">
                    <textarea
                        v-model="nuevoComentario"
                        placeholder="Escriba un mensaje..."
                        rows="3"
                        class="w-full p-3 border rounded-lg focus:ring focus:ring-opacity-50 focus:ring-[#2EBAA1]"
                    ></textarea>
                    <div class="flex justify-end mt-2">
                        <button
                            class="bg-[#2EBAA1] text-white px-6 py-2 rounded-lg"
                            @click="subirComentario"
                        >
                            Enviar
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="comentarios.length > 0">
                <div class="text-gray-700 mb-4">
                    {{ comentarios.length }} comentario<span
                        v-if="comentarios.length > 1"
                        >s</span
                    >
                </div>

                <div
                    v-for="comentario in comentarios"
                    :key="comentario.id"
                    class="flex mb-6 p-4 border-b ml-16"
                >
                    <div class="flex-shrink-0 mr-4">
                        <template v-if="comentario.user?.profile_photo_url">
                            <img
                                class="w-12 h-12 rounded-full object-cover"
                                :src="comentario.user.profile_photo_url"
                                :alt="comentario.user.name"
                            />
                        </template>
                        <template v-else>
                            <div
                                class="flex items-center justify-center w-12 h-12 font-bold text-white bg-gray-500 rounded-full"
                            >
                                {{ getInitials(comentario.user?.name || "U") }}
                            </div>
                        </template>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-[#2EBAA1]">
                            {{ comentario.user?.name || "Usuario Anónimo" }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-1">
                            Publicado el:
                            {{
                                new Date(comentario.created_at).toLocaleString()
                            }}
                        </p>
                        <p class="text-gray-700">{{ comentario.com_texto }}</p>
                    </div>
                </div>
            </div>

            <div v-else>
                <p class="text-gray-500 ml-16">
                    No hay comentarios disponibles para este ticket.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
