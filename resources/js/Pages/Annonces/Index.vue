<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Link, Head } from '@inertiajs/vue3';
import AutoLayout from '@/Layouts/AutoLayout.vue'

const page = usePage();
const annoncesList = ref([...page.props.annonces.data]);
const nextPageUrl = ref(page.props.annonces.next_page_url);
const loading = ref(false);

// Récupérer la bonne image
const getImage = (annonce) => {
  return annonce.images?.[0]?.path
    ? `/storage/${annonce.images[0].path.replace('public/', '')}`
    : 'https://via.placeholder.com/400x300?text=Annonce';
};

// Charger plus d’annonces via axios
const loadMore = async () => {
  if (!nextPageUrl.value || loading.value) return;

  loading.value = true;

  try {
    const response = await axios.get(nextPageUrl.value);
    const newAnnonces = response.data.annonces.data;

    annoncesList.value.push(...newAnnonces);
    nextPageUrl.value = response.data.annonces.next_page_url;
  } catch (error) {
    console.error('Erreur lors du chargement des annonces :', error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <Head title="Nos annonces" />

  <AutoLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Nos annonces
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="annonce in annoncesList"
            :key="annonce.id"
            class="bg-white rounded-2xl shadow-md overflow-hidden"
          >
            <img
              :src="annonce.first_image || getImage(annonce)"
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
                <span class="text-lg font-bold text-blue-600">
                  {{ annonce.price_per_night }} € / nuit
                </span>
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

        <!-- Bouton Charger plus -->
        <div class="text-center mt-8">
          <button
            v-if="nextPageUrl && !loading"
            @click="loadMore"
            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
          >
            Charger plus
          </button>
          <span v-if="loading" class="text-gray-500">Chargement...</span>
        </div>
      </div>
    </div>
  </AutoLayout>
</template>