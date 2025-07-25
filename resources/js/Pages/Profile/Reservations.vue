<script setup>
import { usePage } from '@inertiajs/vue3'
import ReservationCard from '@/Components/ReservationCard.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import CommentModal from '@/Components/CommentModal.vue'

const { reservationsActives, reservationsPassees } = usePage().props

const showModal = ref(false)
const selectedReservation = ref(null)

function openCommentModal(reservation) {
  selectedReservation.value = reservation
  showModal.value = true
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="'Reservations'" />

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-12">
      <!-- Titre -->
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes réservations</h1>

      <!-- Réservations à venir -->
      <section v-if="reservationsActives.length">
        <h2 class="text-2xl font-semibold text-700 mb-6">Vos prochains voyages</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ReservationCard
            v-for="reservation in reservationsActives"
            :key="reservation.id"
            :reservation="reservation"
            status="active"
          />
        </div>
      </section>

      <!-- Réservations passées -->
      <section v-if="reservationsPassees.length">
        <h2 class="text-2xl font-semibold text-gray-700 mt-12 mb-6">Historique</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ReservationCard
            v-for="reservation in reservationsPassees"
            :key="reservation.id"
            :reservation="reservation"
            status="past"
            @on-comment="openCommentModal"
          />
        </div>
      </section>

      <!-- Aucun résultat -->
      <div v-if="!reservationsActives.length && !reservationsPassees.length" class="text-center text-gray-500 py-24">
        <p class="text-xl">Aucune réservation enregistrée pour le moment.</p>
      </div>
    </div>

    <!-- ✅ Modale de commentaire -->
    <CommentModal
      v-if="showModal"
      :show="showModal"
      :reservation-id="selectedReservation?.annonce?.id"
      :annonce-title="selectedReservation?.annonce?.title"
      @close="showModal = false"
    />
  </AuthenticatedLayout>
</template>