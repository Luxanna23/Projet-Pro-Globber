<script setup>
import AutoLayout from '@/Layouts/AutoLayout.vue'
import Footer from '@/Components/Footer.vue'
import { ref, computed, onMounted, watch } from 'vue'
import heroImage from '@/Assets/welcome.png'
import BlocAppPromotion from '@/Components/BlocAppPromotion.vue'
import { usePage } from '@inertiajs/vue3'
import BlocDestinationsPopulaires from '@/Components/BlocDestinationsPopulaires.vue'

const darkMode = ref(false)

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (
    savedTheme === 'dark' ||
    (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)
  ) {
    darkMode.value = true
  }
})

watch(darkMode, (val) => {
  if (val) {
    document.body.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.body.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
})

const heroBackground = computed(() => {
  return {
    backgroundImage: `linear-gradient(to right, rgba(255, 255, 255, 0.9) 40%, rgba(255, 255, 255, 0.7) 60%, rgba(255, 255, 255, 0) 100%), url('${heroImage}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  }
})


const destinations = usePage().props.destinations.map(dest => {
  const formatted = dest.city.charAt(0).toUpperCase() + dest.city.slice(1).toLowerCase()
  const fileName = `${formatted}.jpg`

  return {
    name: dest.city,
    image: new URL(`../assets/Destinations/${fileName}`, import.meta.url).href,
    properties: dest.total
  }
})
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

          <div class="bg-white p-4 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Destination -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Destination</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-map-pin-line text-gray-400"></i>
                  </div>
                  <input
                    type="text"
                    class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none"
                    placeholder="Où allez-vous?"
                  />
                </div>
              </div>

              <!-- Dates -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Dates</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-calendar-line text-gray-400"></i>
                  </div>
                  <input
                    type="text"
                    class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none"
                    placeholder="Arrivée - Départ"
                  />
                </div>
              </div>

              <!-- Voyageurs -->
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Voyageurs</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ri-user-line text-gray-400"></i>
                  </div>
                  <select
                    class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none pr-8 appearance-none"
                  >
                    <option>1 voyageur</option>
                    <option>2 voyageurs</option>
                    <option>3 voyageurs</option>
                    <option>4 voyageurs</option>
                    <option>5+ voyageurs</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <i class="ri-arrow-down-s-line text-gray-400"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Rechercher -->
            <div class="mt-4 flex justify-end">
              <button class="px-6 py-3 bg-primary text-white rounded-button flex items-center whitespace-nowrap">
                <i class="ri-search-line mr-2"></i>
                Rechercher
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <BlocDestinationsPopulaires :destinations="destinations" />
    <BlocAppPromotion />
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