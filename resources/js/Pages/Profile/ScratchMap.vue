<script setup>
import { onMounted, ref } from 'vue'
import * as d3 from 'd3'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  visitedCountries: Array,
})

const svgRef = ref(null)

onMounted(async () => {
  const width = 960
  const height = 500

  const projection = d3.geoNaturalEarth1()
    .scale(160)
    .translate([width / 2, height / 2])

  const path = d3.geoPath().projection(projection)

  const svg = d3.select(svgRef.value)
    .attr('viewBox', [0, 0, width, height])

  const geojson = await d3.json('/geo/countries.geojson')

  // Debug : vérifier si un pays est reconnu
  console.log('Exemple properties:', geojson.features[0].properties)

  // Création d'un Set pour meilleure performance
  const visitedSet = new Set(props.visitedCountries.map(c => c.toUpperCase()))

  svg.selectAll('path')
    .data(geojson.features)
    .enter()
    .append('path')
    .attr('d', path)
    .attr('fill', d => {
      const iso = d.properties['ISO3166-1-Alpha-2']
      return visitedSet.has(iso) ? '#4ade80' : '#e5e7eb'
    })
    .attr('stroke', '#999')
    .attr('stroke-width', 0.5)
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Scratch Map" />

    <div class="p-8">
      <div class="max-w-7xl mx-auto px-4 py-12 space-y-8">
        <h1 class="text-3xl font-bold text-gray-900">🌍 My Map</h1>
        <svg ref="svgRef" class="w-full max-w-5xl mx-auto h-[500px]" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>