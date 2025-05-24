<script setup>
import promoImage from '@/Assets/promo.jpg';
import Logo from '@/Assets/Logo.png';
</script>

<template>
  <div class="max-w-6xl mx-auto my-16 bg-white rounded-[24px] overflow-hidden grid grid-cols-1 lg:grid-cols-2" style="background-color: #FCFCFE">
    <!-- Colonne gauche -->
    <div class="bg-gray-900 text-white p-10 flex items-center justify-center">
      <div class="w-full max-w-md text-center">
        <img :src="Logo" alt="Logo Globber" class="h-8 mx-auto mb-6 object-contain" />

        <h2 class="text-2xl font-bold mb-4">Téléchargez notre application</h2>
        <p class="text-sm text-gray-300 mb-6 leading-relaxed">
          Réservez, gérez vos voyages et communiquez avec vos hôtes où que vous soyez. <br />
          Notre application rend tout plus simple.
        </p>

        <div class="flex justify-center gap-4 flex-wrap">
          <a
            href="#"
            class="flex items-center gap-2 bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-semibold shadow hover:bg-gray-200 transition"
          >
            <i class="ri-apple-line text-xl"></i>
            App Store
          </a>
          <a
            href="#"
            class="flex items-center gap-2 bg-white text-gray-900 px-4 py-2 rounded-lg text-sm font-semibold shadow hover:bg-gray-200 transition"
          >
            <i class="ri-google-play-line text-xl"></i>
            Google Play
          </a>
        </div>
      </div>
    </div>

    <!-- Colonne droite -->
    <div class="flex items-center justify-center p-10" style="background-color: #FCFCFE">
        <img
            :src="promoImage"
            alt="Promo application Globber"
            class="w-[240px] md:w-[300px] lg:w-[340px]"
        />
    </div>
  </div>
</template>

<style scoped>
@import url("https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css");
</style>