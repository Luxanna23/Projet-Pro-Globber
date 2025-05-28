<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import { Pagination, Navigation } from 'swiper/modules';
import VueEasyLightbox from 'vue-easy-lightbox';
import AutoLayout from '@/Layouts/AutoLayout.vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'
import { Head, Link, useForm,usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, nextTick, watch  } from 'vue';
import { isWithinInterval, parseISO } from 'date-fns';
import axios from 'axios';

const props = defineProps({
    annonce: Object,
    user: Object,    
    calendrier: Array,
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

const handleRangeSelect = ([start, end]) => {
  form.start_date = start
  form.end_date = end
}

const submit = () => {
  form.post(route('annonces.reserve', annonce.id), {
    preserveScroll: true,
  })
}

const isDateIn = (date, type) => {
  return (props.calendrier ?? []).some(p => {
    if (p.type !== type) return false

    const start = parseISO(p.start)
    const end = parseISO(p.end)

    return isWithinInterval(date, { start, end })
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

const startDateRef = ref(null)
const endDateRef = ref(null)

const reservedPeriods = computed(() =>
  (props.calendrier ?? [])
    .filter(p => p.type === 'réservé')
    .map(p => ({
      start: new Date(p.start),
      end: new Date(p.end),
    }))
)

// Fonction de blocage de date
const isDateDisabled = (date) => {
  const d = new Date(date)

  const dansReservation = reservedPeriods.value.some(p => d >= p.start && d <= p.end)

  const dansDisponibilite = (props.calendrier ?? [])
    .filter(p => p.type === 'disponible')
    .some(p => {
      const start = new Date(p.start)
      const end = new Date(p.end)
      return d >= start && d <= end
    })

  // On désactive la date si elle est réservée OU non disponible
  return dansReservation || !dansDisponibilite
}

// Synchronisation ref -> form
watch(startDateRef, (val) => {
  form.start_date = val ? val.toISOString().split('T')[0] : ''
  // Reset end_date si elle devient invalide
  if (endDateRef.value && endDateRef.value <= val) {
    endDateRef.value = null
  }
})

watch(endDateRef, (val) => {
  form.end_date = val ? val.toISOString().split('T')[0] : ''
})

//paiement
const launchCheckout = async () => {
  if (!form.start_date || !form.end_date) {
    alert("Veuillez sélectionner des dates valides");
    return;
  }

  try {
    const response = await axios.post('/checkout', {
      annonce_id: annonce.id,
      start_date: form.start_date,
      end_date: form.end_date,
      price: total.value,
    })

    window.location.href = response.data.url
  } catch (err) {
    console.error("Erreur de checkout", err)
  }
}

</script>

<template>
    <AutoLayout>
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
            <p><strong>Ville:</strong> {{ annonce.city }}</p>
            <p><strong>Pays:</strong> {{ annonce.country }}</p>
          </div>
  
          <!-- Calendrier visuel -->
          <div class=" p-6 w-full">
            <h2 class="text-xl font-semibold mb-4">Disponibilités de l'annonce</h2>

            <VCalendar :columns="1" :readonly="true" class="w-full">
              <template #day-content="{ day }">
                <div
                  :class="[
                    'w-8 h-8 flex items-center justify-center rounded-full text-sm',
                    isDateIn(day.date, 'réservé') ? 'bg-red-200 text-red-800 font-bold' : '',
                    isDateIn(day.date, 'disponible') ? 'bg-green-200 text-green-800 font-bold' : ''
                  ]"
                >
                  {{ day.day }}
                </div>
              </template>
            </VCalendar>

            <div class="mt-4 text-sm text-gray-600">
              <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span> Disponible
              <span class="inline-block w-3 h-3 bg-red-500 rounded-full ml-4 mr-1"></span> Réservé
            </div>
          </div>

          <!-- Avis -->
          <div>
            <h2 class="text-xl font-semibold mt-10 mb-4">Avis</h2>
  
            <div v-if="annonce.comments.length">
              <div
                v-for="(comment, i) in annonce.comments"
                :key="i"
                class="bg-white rounded-xl border p-4 mb-4 shadow"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="font-bold text-gray-800">{{ comment.user }}</span>
                  <span class="text-yellow-500">
                    ⭐ {{ comment.rating }} / 5
                  </span>
                </div>
                <p class="text-gray-700">{{ comment.content }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ comment.created_at }}</p>
              </div>
            </div>
            <div v-else class="text-gray-500 italic">
              Aucun avis pour le moment.
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
                      v-model="startDateRef"
                      :enable-time-picker="false"
                      :min-date="new Date()"
                      :disabled-dates="isDateDisabled"
                      placeholder="Date d'arrivée"
                      auto-apply
                      :format="'dd/MM/yyyy'"
                      hide-navigation=false
                      class="w-full"
                    />
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Départ</label>
                    <Datepicker
                      v-model="endDateRef"
                      :enable-time-picker="false"
                      :min-date="startDateRef || new Date()"
                      :disabled-dates="isDateDisabled"
                      placeholder="Date de départ"
                      auto-apply
                      :format="'dd/MM/yyyy'"
                      hide-navigation=false
                      class="w-full"
                    />
                  </div>
                </div>

                <div>
                    <label class="block text-sm">Voyageurs</label>
                    <select v-model="voyageurs" class="w-full bg-white-100 text-grey rounded-md p-2 mt-1">
                    <option value="1">1 voyageur</option>
                    <option value="2">2 voyageurs</option>
                    <option value="3">3 voyageurs</option>
                    </select>
                </div>

                <!-- Bouton -->
                <button
                  @click="launchCheckout"
                  class="w-full bg-[#6439FF] hover:bg-[#A594F9] text-white py-2 rounded-md text-lg font-semibold" 
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
    </AutoLayout>
  </template>