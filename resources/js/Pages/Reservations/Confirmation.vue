<script setup>

import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const reservation = usePage().props.reservation

const formatDate = (date) =>
  new Date(date).toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })

</script>

<template>
  <AuthenticatedLayout>
  <div
  class="min-h-screen bg-center bg-cover relative"
  :style="{ backgroundImage: 'url(' + reservation.annonce.first_image + ')' }"
>
    <!-- Overlay blanc semi-transparent -->
    <div class="absolute inset-0 bg-white/80 backdrop-blur-sm"></div>

    <!-- Contenu principal -->
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4">
      <div class="bg-white rounded-2xl shadow-xl max-w-xl w-full p-8 text-center border border-green-100">

        <!-- Icône -->
        <div class="flex justify-center mb-6">
          <div class="bg-green-100 text-green-600 rounded-full p-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </div>

        <h1 class="text-2xl font-bold text-green-600 mb-2">Réservation confirmée</h1>
        <p class="text-gray-600 mb-6">
          Merci <strong>{{ reservation.user.firstname }}</strong>, votre séjour à <strong>{{ reservation.annonce.city }}</strong> à été réservé ! 🎉
        </p>

        <!-- Infos -->
        <div class="text-left text-sm text-gray-800 bg-gray-100 rounded-xl p-4 mb-6 space-y-2">
          <p><strong>Annonce :</strong> {{ reservation.annonce.title }}</p>
          <p><strong>Lieu :</strong> {{ reservation.annonce.city }}, {{ reservation.annonce.country }}</p>
          <p><strong>Dates :</strong> du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}</p>
          <p><strong>Prix total :</strong> {{ reservation.price }} €</p>
        </div>

        <!-- Boutons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <a href="/profile/reservations" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Voir mes réservations
          </a>
          <a href="/annonces" class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
            Retour à l'accueil
          </a>
        </div>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>
