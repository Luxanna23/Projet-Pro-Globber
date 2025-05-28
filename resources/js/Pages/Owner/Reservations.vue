<script setup>
import { usePage } from '@inertiajs/vue3'
import ReservationCardOwner from '@/Components/ReservationCardOwner.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Nouveau composant d'encapsulation stylée
import SectionBlock from '@/Components/SectionBlock.vue'

const reservations = usePage().props.reservations
</script>

<template>
    <AuthenticatedLayout>
  <div class="max-w-7xl mx-auto px-4 py-12 space-y-16">

    <div>
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Réservations reçues</h1>
    </div>

    <!-- Section en attente -->
    <SectionBlock title="En attente de confirmation" color="blue">
      <div v-if="reservations.pending?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ReservationCardOwner
          v-for="r in reservations.pending"
          :key="r.id"
          :reservation="r"
          show-actions
        />
      </div>
      <p v-else class="text-gray-400 text-sm">Aucune réservation en attente.</p>
    </SectionBlock>

    <!-- Confirmées -->
    <SectionBlock title="Confirmées" color="green">
      <div v-if="reservations.accepted?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ReservationCardOwner
          v-for="r in reservations.accepted"
          :key="r.id"
          :reservation="r"
        />
      </div>
      <p v-else class="text-gray-400 text-sm">Aucune réservation confirmée.</p>
    </SectionBlock>

    <!-- Refusées -->
    <SectionBlock title="Refusées" color="red">
      <div v-if="reservations.refused?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ReservationCardOwner
          v-for="r in reservations.refused"
          :key="r.id"
          :reservation="r"
        />
      </div>
      <p v-else class="text-gray-400 text-sm">Aucune réservation refusée.</p>
    </SectionBlock>

    <!-- Terminées -->
    <SectionBlock title="Terminées" color="gray">
      <div v-if="reservations.finished?.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ReservationCardOwner
          v-for="r in reservations.finished"
          :key="r.id"
          :reservation="r"
        />
      </div>
      <p v-else class="text-gray-400 text-sm">Aucune réservation terminée.</p>
    </SectionBlock>

  </div>

    </AuthenticatedLayout>
</template>

