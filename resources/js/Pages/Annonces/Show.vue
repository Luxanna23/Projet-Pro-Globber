<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    annonce: Object, // contient toutes les infos de l'annonce
    user: Object,     // l'utilisateur connecté
});

const mainImage = ref(props.annonce.image_urls ? props.annonce.image_urls[0] : null);
const otherImages = ref(props.annonce.image_urls ? props.annonce.image_urls.slice(1) : []);

const changeMainImage = (imageUrl) => {
    mainImage.value = imageUrl;
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="annonce.title" />
        <div class="py-12 bg-gray-100">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 bg-white shadow-md rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="rounded-lg overflow-hidden shadow-md border mb-4">
                            <img :src="mainImage" alt="Image principale de l'annonce" class="w-full h-auto object-cover aspect-video" />
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div
                                v-for="(image, index) in otherImages"
                                :key="index"
                                class="rounded-lg overflow-hidden shadow-sm border cursor-pointer hover:opacity-90 transition duration-200"
                                @click="changeMainImage(image)"
                            >
                                <img :src="image" alt="Miniature de l'annonce" class="w-full h-24 object-cover" />
                            </div>
                            <div v-if="otherImages.length < 2"></div>
                            <div v-if="otherImages.length < 1"></div>
                        </div>
                    </div>

                    <div class="md:flex md:justify-between md:items-start">
                        <div class="mb-6 md:mr-8">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ annonce.title }}</h1>
                            <p class="text-gray-600 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.995 1.995 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ annonce.city }}, {{ annonce.country }}
                            </p>
                            <div class="mb-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-1">Description</h2>
                                <p class="text-gray-800 leading-relaxed">{{ annonce.description }}</p>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-1">Détails</h2>
                                <ul class="text-gray-700 space-y-2 text-sm">
                                    <li><strong class="font-semibold">Adresse :</strong> {{ annonce.address }}, {{ annonce.postal_code }} {{ annonce.city }}</li>
                                    <li><strong class="font-semibold">Pays :</strong> {{ annonce.country }}</li>
                                    <li><strong class="font-semibold">Prix / nuit :</strong> {{ annonce.price_per_night }}€</li>
                                    </ul>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 rounded-lg shadow-md border w-full md:w-auto md:sticky md:top-6">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                                {{ annonce.price_per_night }}€ <span class="text-lg text-gray-500">/ nuit</span>
                            </h2>
                            <button class="w-full bg-rose-500 text-white py-3 px-6 rounded-lg hover:bg-rose-600 transition duration-300">
                                Réserver
                            </button>
                            <div class="mt-4 text-sm text-gray-700">
                                {/* Ajouter ici des informations supplémentaires comme la disponibilité, les taxes, etc. */}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>