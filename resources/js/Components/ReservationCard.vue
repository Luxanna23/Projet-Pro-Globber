<script setup>
const props = defineProps({
  reservation: Object,
  status: String // "active" ou "past"
})

function formatDate(date) {
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <div class="relative bg-white border border-gray-200 rounded-xl shadow overflow-hidden transition hover:shadow-lg">
    <div class="relative h-48 overflow-hidden">
      <img
        :src="reservation.annonce.first_image"
        alt="Photo de l'annonce"
        class="w-full h-full object-cover"
      />
      <div
        v-if="status === 'active'"
        class="absolute top-2 right-2 flex items-center gap-1 text-xs font-medium px-2 py-1 rounded shadow"
        :class="{
        'bg-blue-100 text-blue-800': reservation.status === 'pending',
        'bg-green-100 text-green-800': reservation.status === 'accepted',
        'bg-red-100 text-red-800': reservation.status === 'refused',
        'bg-gray-100 text-gray-600': reservation.status === 'cancelled',
        }"
        >
        <span v-if="reservation.status === 'pending'">🕒</span>
        <span v-else-if="reservation.status === 'accepted'">✅</span>
        <span v-else-if="reservation.status === 'refused'">❌</span>
        <span v-else-if="reservation.status === 'cancelled'">🚫</span>

        {{
            reservation.status === 'pending' ? 'En attente' :
            reservation.status === 'accepted' ? 'Confirmée' :
            reservation.status === 'refused' ? 'Refusée' : 'Annulée'
        }}
      </div>
      <div
        v-if="status === 'past'"
        class="absolute top-2 right-2 bg-gray-600 text-white text-xs px-2 py-1 rounded shadow"
      >
        Terminé
      </div>
    </div>
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-800 mb-1">
        {{ reservation.annonce.title }}
      </h3>
      <p class="text-sm text-gray-500 mb-2">
        {{ reservation.annonce.city }}, {{ reservation.annonce.country }}
      </p>
      <p class="text-sm text-gray-700">
        Du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}
      </p>
      <p class="mt-2 text-sm font-bold text-blue-600">
        {{ reservation.price }} €
      </p>


      <!-- Bouton laisser un commentaire, seulement si c'est une réservation passée et non commentée -->
      <div
        v-if="status === 'past' && !reservation.has_commented"
        class="mt-4"
      >
        <button
          @click="$emit('on-comment', reservation)"
          class="text-indigo-600 text-sm hover:underline"
        >
          💬 Laisser un avis
        </button>
      </div>

    </div>
  </div>
</template>
