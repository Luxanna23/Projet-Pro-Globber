<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import { Pagination, Navigation } from 'swiper/modules';
import VueEasyLightbox from 'vue-easy-lightbox';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Datepicker from 'vue3-datepicker';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed  } from 'vue';

const props = defineProps({
    annonce: Object, // contient toutes les infos de l'annonce
    user: Object,     // l'utilisateur connecté
});

const modules = [Pagination, Navigation]
const showLightbox = ref(false)
const currentIndex = ref(0)

const cleaningFee = 150 // frais de ménage
const taxes = 17
const voyageurs = ref(1)
const { annonce } = props

const openLightbox = (index) => {
  currentIndex.value = index
  showLightbox.value = true
}

const form = useForm({
  start_date: '',
  end_date: '',
})

const formRange = ref(null)

const handleRangeSelect = ([start, end]) => {
  form.start_date = start
  form.end_date = end
}

const submit = () => {
  form.post(route('annonces.reserve', annonce.id), {
    preserveScroll: true,
  })
}

const nights = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  const diff = (new Date(form.end_date) - new Date(form.start_date)) / (1000 * 60 * 60 * 24)
  return diff > 0 ? diff : 0
})

const totalNuit = computed(() => nights.value * annonce.price_per_night)

const total = computed(() => {
  const rawTotal = totalNuit.value + cleaningFee + taxes;
  return Number(rawTotal.toFixed(2));
  //toFixed retourne une chaîne, donc on utilise Number() pour le convertir à nouveau en nombre.
});

</script>

<template>
    <AuthenticatedLayout>
      <Head :title="annonce.title" />
  
      <!-- Image en carousel avec swiper -->
      <div class="max-w-7xl mx-auto px-4 pt-10 space-y-6">
        <Swiper
            :modules="modules"
            :slides-per-view="1"
            :space-between="10"
            navigation
            pagination
            class="rounded-xl overflow-hidden shadow"
        >
            <SwiperSlide
                v-for="(url, index) in annonce.image_urls"
                :key="index"
                @click="openLightbox(index)"
                class="cursor-zoom-in"
            >
                <img :src="url" class="w-full h-[400px] object-cover" />
            </SwiperSlide>
        </Swiper>

            <!-- Lightbox -->
        <VueEasyLightbox
            :visible="showLightbox"
            :imgs="annonce.image_urls"
            :index="currentIndex"
            @hide="showLightbox = false"
        />
      </div>
  
      <!-- Contenu -->
      <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Infos principales -->
        <div class="lg:col-span-2 space-y-6">
          <div>
            <h1 class="text-3xl font-bold">{{ annonce.title }}</h1>
            <p class="text-gray-600">
              Logement entier · {{ annonce.city }}, {{ annonce.country }}
            </p>
          </div>
  
          <div>
            <p class="text-gray-800 leading-relaxed">
              {{ annonce.description }}
            </p>
          </div>
  
          <div class="grid grid-cols-2 gap-4 text-gray-700">
            <p><strong>Adresse:</strong> {{ annonce.address }}</p>
            <p><strong>Code postal:</strong> {{ annonce.postal_code }}</p>
            <p><strong>Prix:</strong> {{ annonce.price_per_night }}€ / nuit</p>
          </div>
  
          <!-- Placeholder calendrier -->
          <div class="border rounded-xl p-6 bg-gray-100 text-center text-gray-500 italic">
            📅 Calendrier de réservation (à venir)
          </div>
  
          <!-- Commentaires fictifs -->
          <div>
            <h2 class="text-xl font-semibold mt-10 mb-4">Commentaires</h2>
  
            <div class="space-y-4">
              <div class="bg-white p-4 rounded-xl shadow">
                <p class="font-semibold">Florent · octobre 2024</p>
                <p class="text-gray-600 text-sm mb-2">Séjour d'une nuit</p>
                <p>Génial ! On a passé un super weekend, les installations de la maison sont incroyables !</p>
              </div>
  
              <div class="bg-white p-4 rounded-xl shadow">
                <p class="font-semibold">Jeremy · août 2024</p>
                <p class="text-gray-600 text-sm mb-2">Séjour d'une nuit</p>
                <p>Un séjour agréable, des hôtes accueillants, les équipements avec confiance et sympathie !</p>
              </div>
            </div>
          </div>
        </div>
  
        <!-- colonne a droite -->
        <div class="space-y-4">

            <!-- Prix -->
            <div>
                <h2 class="text-2xl font-bold">
                    {{ annonce.price_per_night }} €
                    <span class="text-sm text-gray-500">/ nuit</span>
                </h2>
            </div>

            <!-- Bloc de réservation  -->
            <div class="bg-white/10 backdrop-blur-md p-6 rounded-xl shadow space-y-4">

                <!-- Sélecteur de dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Arrivée</label>
                        <Datepicker
                        v-model="form.start_date"
                        :enable-time-picker="false"
                        :min-date="new Date()"
                        placeholder="Date d'arrivée"
                        class="w-full"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                        <Datepicker
                        v-model="form.end_date"
                        :enable-time-picker="false"
                        :min-date="form.start_date || new Date()"
                        placeholder="Date de départ"
                        class="w-full"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm">Voyageurs</label>
                    <select v-model="voyageurs" class="w-full bg-gray-800 text-white rounded-md p-2 mt-1">
                    <option value="1">1 voyageur</option>
                    <option value="2">2 voyageurs</option>
                    <option value="3">3 voyageurs</option>
                    </select>
                </div>

                <!-- Bouton -->
                <button
                    @click="submit"
                    class="w-full bg-[#6439FF] hover:bg-[#A594F9] text-white py-2 rounded-md text-lg font-semibold" 
                    :disabled="form.processing"
                >
                    Réserver
                </button>

                <p class="text-xs text-gray-400 text-center">Aucun montant ne vous sera débité pour le moment</p>

                <!-- Détail du prix -->
                <div class="border-t border-gray-700 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                    <span>{{ annonce.price_per_night }} € x {{ nights }} nuits</span>
                    <span>{{ totalNuit }} €</span>
                    </div>
                    <div class="flex justify-between underline">
                    <span>Frais de ménage</span>
                    <span>{{ cleaningFee }} €</span>
                    </div>
                    <div class="flex justify-between">
                    <span> TVA </span>
                    <span>{{ taxes }} €</span>
                    </div>
                    <div class="border-t border-gray-700 pt-2 flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span>{{ total }} €</span>
                    </div>
                </div>
            </div>
        </div>

      </div>
    </AuthenticatedLayout>
  </template>