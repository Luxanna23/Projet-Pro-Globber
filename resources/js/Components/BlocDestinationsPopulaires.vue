<script setup>
defineProps({
  destinations: Array
})
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-16">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">
      <span class="text-black-700 px-2 py-1 rounded">Destinations populaires</span>
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="(destination, index) in destinations"
        :key="index"
        class="relative rounded-xl overflow-hidden shadow hover:shadow-lg transition duration-200"
      >
        <img
          :src="destination.image"
          :alt="destination.name"
          class="w-full h-40 object-cover"
        />
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent text-white p-4">
          <h3 class="text-lg font-semibold">{{ destination.name }}</h3>
          <p class="text-sm text-gray-200">{{ destination.properties.toLocaleString() }} propriétés</p>
        </div>
      </div>
    </div>
  </div>
</template>