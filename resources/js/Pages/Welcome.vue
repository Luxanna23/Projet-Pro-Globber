<script setup>
import AutoLayout from '@/Layouts/AutoLayout.vue'
import Footer from '@/Components/Footer.vue'
import { ref, computed, onMounted, watch } from 'vue'
import heroImage from '@/Assets/welcome.png'
import BlocAppPromotion from '@/Components/BlocAppPromotion.vue'
import { usePage, useForm } from '@inertiajs/vue3'
import BlocDestinationsPopulaires from '@/Components/BlocDestinationsPopulaires.vue'

const heroBackground = computed(() => {
  return {
    backgroundImage: `linear-gradient(to right, rgba(255, 255, 255, 0.9) 40%, rgba(255, 255, 255, 0.7) 60%, rgba(255, 255, 255, 0) 100%), url('${heroImage}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  }
})


const images = import.meta.glob('../assets/Destinations/*.jpg', { eager: true })

const destinations = usePage().props.destinations.map(dest => {
  const city = dest.city
  const imageKey = `../assets/Destinations/${city}.jpg`

  return {
    name: city,
    image: images[imageKey]?.default ?? '/Assets/default.jpg', // fallback si manquant
    properties: dest.total
  }
})

const form = useForm({
  destination: '',
  start_date: '',
  end_date: '',
})

const villesDisponibles = [
  'Paris', 'London', 'Berlin', 'Rome', 'Madrid', 'Lisbon',
  'Ottawa', 'Washington', 'Tokyo', 'Beijing', 'Seoul', 'Moscow',
  'Monaco', 'Brasilia', 'Marrakech', 'Cairo',
  'Bangkok', 'Singapore', 'Dubai', 'Istanbul', 'Amsterdam',
]

const suggestions = computed(() => {
  if (!form.destination) return []
  const query = form.destination.toLowerCase()
  return villesDisponibles.filter(ville =>
    ville.toLowerCase().includes(query)
  )
})

const showSuggestions = ref(false)
const selectSuggestion = (ville) => {
  form.destination = ville
  showSuggestions.value = false
}
</script>

<template>
  <AutoLayout>
    <!-- Hero Section -->
    <section class="hero-section py-16 md:py-24" :style="heroBackground">
      <div class="container mx-auto px-4 w-full">
        <div class="max-w-2xl">
          <h1 class="text-4xl md:text-5xl font-bold mb-6">
            Trouvez votre prochaine destination
          </h1>
          <p class="text-xl text-gray-700 mb-8">
            Avec Globber, découvrez le monde comme jamais auparavant.<br />
            Explorez des logements uniques et vivez des expériences authentiques.
          </p>

          <div class="bg-white rounded-xl shadow-md px-6 py-4 mt-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">

              <!-- Destination -->
              <div class="relative">
                
                <input
                  v-model="form.destination"
                  @input="showSuggestions = true"
                  @focus="showSuggestions = true"
                  @blur="setTimeout(() => showSuggestions = false, 100)"
                  type="text"
                  class="pl-10 py-3 w-full rounded-md bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:outline-none"
                  placeholder="Où allez-vous ?"
                />
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="ri-map-pin-line text-gray-400 text-lg"></i>
                </span>

                <!-- Suggestions -->
                <ul
                  v-if="showSuggestions && suggestions.length"
                  class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg"
                >
                  <li
                    v-for="(ville, index) in suggestions"
                    :key="index"
                    @mousedown.prevent="selectSuggestion(ville)"
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                  >
                    {{ ville }}
                  </li>
                </ul>
              </div>

              <!-- Dates -->
              <div class="space-y-1">
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-calendar-line text-gray-400 text-lg"></i>
                  </span>
                  <input
                    v-model="form.start_date"
                    type="date"
                    class="pl-10 py-3 w-full ..."
                    placeholder="Dates"
                  />
                </div>
              </div>

              <!-- Voyageurs -->
              <div class="space-y-1">
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-user-line text-gray-400 text-lg"></i>
                  </span>
                  <select
                    class="pl-10 pr-8 py-3 w-full rounded-md bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:outline-none appearance-none"
                  >
                    <option>1 voyageur</option>
                    <option>2 voyageurs</option>
                    <option>3+ voyageurs</option>
                  </select>
                  <span class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="ri-arrow-down-s-line text-gray-400 text-lg"></i>
                  </span>
                </div>
              </div>

            </div>
          </div>
          

            <!-- Rechercher -->
            <div class="mt-4 flex justify-end">
              <button
                @click="form.get(route('annonces.search'))"
                class="px-6 py-3 bg-[#6439FF] ..."
              >
                <i class="ri-search-line mr-2"></i>
                Rechercher
              </button>
            </div>
          </div>
        </div>
    </section>
    <BlocDestinationsPopulaires :destinations="destinations" />
    <BlocAppPromotion />

    <!-- Bloc Newsletter -->
    <section class=" py-12" style="background-color: #ADB2D4; opacity: 90%;">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Restez informé des meilleures offres</h2>
        <p class="text-white-600 dark:text-white-300 mb-6">
          Inscrivez-vous à notre newsletter pour recevoir des offres exclusives et des inspirations de voyage.
        </p>
        <div class="max-w-md mx-auto flex flex-col sm:flex-row items-center gap-2">
          <input
            type="email"
            placeholder="Entrez votre adresse e-mail"
            class="w-full px-4 py-3 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-primary focus:outline-none"
          />
          <button
            class="w-full sm:w-auto px-6 py-3 bg-primary text-dark rounded-md hover:bg-opacity-90 transition"
          >
            S'inscrire
          </button>
        </div>
      </div>
    </section>

    <Footer />
  </AutoLayout>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Pacifico&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
@import url("https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css");

body {
  font-family: "Poppins", sans-serif;
}
</style>