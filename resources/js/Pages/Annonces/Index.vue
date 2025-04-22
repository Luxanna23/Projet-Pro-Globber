<script setup>
import { Link } from "@inertiajs/vue3";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
defineProps({
    annonces: Array,
});

const getImage = (annonce) => {
    return annonce.images?.[0]?.path
        ? `/storage/${annonce.images[0].path.replace("public/", "")}`
        : "https://via.placeholder.com/400x300?text=Annonce";
};
</script>

<template>
    <Head title="annonces" />

<AuthenticatedLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nos annonces</h2>
    </template>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    v-for="annonce in annonces"
                    :key="annonce.id"
                    class="bg-white rounded-2xl shadow-md overflow-hidden"
                >
                    <img
                        :src="annonce.first_image"
                        alt="Annonce"
                        class="w-full h-48 object-cover"
                    />
                    <div class="p-4">
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ annonce.title }}
                        </h2>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ annonce.description.slice(0, 100) }}...
                        </p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-lg font-bold text-blue-600"
                                >{{ annonce.price_per_night }} € / nuit</span
                            >
                            <Link
                                :href="route('annonces.show', annonce.id)"
                                class="text-sm text-blue-500 hover:underline"
                            >
                                Voir plus
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</AuthenticatedLayout>
</template>