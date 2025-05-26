<script setup>
import { onMounted, ref } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  visitedCountries: Array,
  userName: String,
  visitedCount: Number,
  visitedPercent: Number
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

  svg.selectAll('path')
    .data(geojson.features)
    .enter()
    .append('path')
    .attr('d', path)
    .attr('fill', d => props.visitedCountries.includes(d.properties['ISO3166-1-Alpha-2']) ? '#4ade80' : '#e5e7eb')
    .attr('stroke', '#999')
    .attr('stroke-width', 0.5)
})
</script>

<template>
  <div id="share-map-container" class="bg-white text-black min-h-screen flex flex-col items-center py-10 space-y-6">
    <img src="/images/logo.png" alt="Globber Logo" class="w-40 mb-4" />
    <h1 class="text-2xl font-semibold">Carte de {{ userName }}</h1>
    <p class="text-gray-600 text-sm">
          {{ visitedCount }} pays visités • {{ visitedPercent }}% du monde
    </p>
    <svg ref="svgRef" class="w-full max-w-5xl h-[500px]" />
  </div>
</template>