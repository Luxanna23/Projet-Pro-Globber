<script setup>
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  reservation: Object,
  showActions: Boolean
})

function formatDate(date) {
  return new Date(date).toLocaleDateString('fr-FR')
}

function updateStatus(status) {
  Swal.fire({
    title: status === 'accepted' ? "Accepter cette réservation ?" : "Refuser cette réservation ?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: status === 'accepted' ? '#10b981' : '#ef4444', // green or red
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Oui, confirmer',
    cancelButtonText: 'Annuler'
  }).then((result) => {
    if (result.isConfirmed) {
      router.visit(
        route('owner.reservations.updateStatus', props.reservation.id),
        {
          method: 'patch',
          data: { status },
          preserveScroll: true,
          onSuccess: () => {
            // ✅ Sweet alert visuel de succès
            Swal.fire({
              icon: 'success',
              title: 'Statut mis à jour !',
              text: `La réservation a bien été ${status === 'accepted' ? 'acceptée' : 'refusée'}.`,
              timer: 1500,
              showConfirmButton: false,
            })

            // ✅ Et on reload manuellement les données
            router.reload({ preserveScroll: true })
          },
        })
    }
  })
}

</script>


<template>
  <div class="bg-white border rounded-xl p-4 shadow space-y-2">
    <h3 class="font-bold text-lg">{{ reservation.annonce.title }}</h3>
    <p class="text-sm text-gray-500">{{ reservation.annonce.city }}, {{ reservation.annonce.country }}</p>
    <p class="text-sm">Du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}</p>
    <p class="text-sm text-gray-700">Locataire : {{ reservation.user.name }}</p>
    <p class="text-sm font-bold text-grey-600">{{ reservation.price }} €</p>

    <div v-if="showActions" class="flex gap-2 pt-2">
      <button
        class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200"
        @click="updateStatus('accepted')"
      >
        Accepter
      </button>
      <button
        class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200"
        @click="updateStatus('refused')"
      >
        Refuser
      </button>
    </div>

    <p v-else class="text-xs mt-2 font-medium uppercase" :class="{
      'text-green-600': reservation.status === 'accepted',
      'text-red-600': reservation.status === 'refused'
    }">
      {{ reservation.status === 'accepted' ? 'Confirmée' : 'Refusée' }}
    </p>
  </div>
</template>

