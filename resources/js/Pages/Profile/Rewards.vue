
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const reservations = usePage().props.reservations || []
const points = computed(() => reservations.length * 25)

const rewards = [
  { points: 100, label: '10% de réduction', icon: '🎁' },
  { points: 200, label: 'Accès prioritaire aux annonces', icon: '🚀' },
  { points: 300, label: 'Badge "Voyageur fidèle"', icon: '🏅' },
  { points: 400, label: '20% de réduction', icon: '💸' },
  { points: 500, label: '1 nuit gratuite', icon: '🛏️' },
  { points: 600, label: '60% de réduction', icon: '✈️' },
  { points: 700, label: 'Upgrade gratuit', icon: '⬆️' },
  { points: 800, label: 'Pack photo souvenir offert', icon: '📸' },
  { points: 900, label: 'Séjour de 2 nuits offert', icon: '🌙' },
  { points: 1000, label: 'Voyage de votre choix offert', icon: '🌍' },
]
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Mes récompenses" />
    <div class="max-w-2xl mx-auto py-12 px-4">
      <h1 class="text-3xl font-bold mb-6 text-center">Mes récompenses</h1>
      <div class="bg-indigo-50 p-4 rounded-xl shadow text-center mb-8">
        <p class="text-xl">Points accumulés :</p>
        <div class="text-5xl font-extrabold text-indigo-600">{{ points }}</div>
      </div>

      <div class="space-y-4">
        <div
          v-for="reward in rewards"
          :key="reward.points"
          class="bg-white border border-gray-200 shadow rounded-lg p-4 relative"
        >
          <div class="flex justify-between items-center mb-2">
            <div class="flex items-center gap-2">
              <span class="text-2xl">{{ reward.icon }}</span>
              <div>
                <h2 class="font-semibold text-lg">{{ reward.label }}</h2>
                <p class="text-sm text-gray-500">{{ reward.points }} points requis</p>
              </div>
            </div>
            <div v-if="points >= reward.points" class="text-green-600 font-bold text-sm">OBTENU</div>
          </div>
          <div class="relative w-full h-3 bg-gray-200 rounded">
            <div
              class="absolute top-0 left-0 h-full bg-indigo-500 rounded"
              :style="{
                width: `${Math.min(100, (points / reward.points) * 100)}%`
              }"
            ></div>
          </div>
          <div class="text-right text-xs mt-1 text-gray-500">
            {{ Math.min(points, reward.points) }} / {{ reward.points }}
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
</style>