<script setup>
defineProps({
  name: String,
  image: String,
  properties: Number
})
</script>

<template>
  <div
    class="relative rounded-xl overflow-hidden shadow hover:shadow-md transition aspect-square"
  >
    <img
      :src="image"
      :alt="name"
      class="w-full h-full object-cover"
    />
    <div
      class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent text-white p-4"
    >
      <h3 class="text-lg font-semibold truncate">{{ name }}</h3>
      <p class="text-sm text-gray-200">{{ properties.toLocaleString() }} propriétés</p>
    </div>
  </div>
</template>