<script setup>
import { onMounted, ref, nextTick, toRef } from 'vue'
import * as d3 from 'd3'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import html2canvas from 'html2canvas'
import ScratchMapShare from './ScratchMapShare.vue'

const props = defineProps({
  visitedCountries: Array
})
console.log("Visited Countries:", props.visitedCountries);


const showHiddenShare = ref(false)
const shareRef = ref(null)

const svgRef = ref(null)

const totalCountries = 193;

const visitedCountries = toRef(props, 'visitedCountries');
const visitedSet = new Set(visitedCountries.value.map(country => country.toUpperCase()));

const visitedCount = props.visitedCountries.length;
const visitedPercent = Math.round((visitedCount / totalCountries) * 100);

onMounted(async () => {
  const width = 960
  const height = 500

  const projection = d3.geoNaturalEarth1()
    .scale(160)
    .translate([width / 2, height / 2])

  const path = d3.geoPath().projection(projection)

  const svg = d3.select(svgRef.value)
    .attr('preserveAspectRatio', 'xMidYMid meet')
    .attr('viewBox', `0 0 ${width} ${height}`)
    .classed('responsive-svg', true)

  const geojson = await d3.json('/geo/countries.geojson')
  // find france in the geojson
  console.log("GeoJSON France:", geojson.features.find(feature => feature.properties['ISO3166-1-Alpha-2'] === 'FR'));

  svg.selectAll('path')
    .data(geojson.features)
    .enter()
    .append('path')
    .attr('d', path)
    .attr('fill', d => {
      const iso = d.properties['ISO3166-1-Alpha-2']
      console.log(`ISO: ${iso}, Visited: ${visitedSet.has(iso)}`);
      
     return visitedSet.has(iso) ? '#4ade80' : '#e5e7eb'
    })
    .attr('stroke', '#999')
    .attr('stroke-width', 0.5)

    console.log("FR", visitedSet.has("FR"));
    
})

//poour l'exportation
const captureShareMap = async () => {
  await nextTick() // s'assure que le DOM du composant est monté
  if (!shareRef.value) return

  html2canvas(shareRef.value, {
    backgroundColor: null, // si tu veux fond transparent
    scale: 2,
  }).then(canvas => {
    const link = document.createElement('a')
    link.download = 'ma-carte-globber.png'
    link.href = canvas.toDataURL('image/png')
    link.click()
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Scratch Map" />

    <div class="max-w-7xl mx-auto px-4 py-12 space-y-8">
      <!-- Titre aligné à gauche comme les autres pages -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Ma Scratch Map</h1>
      </div>

      <div id="scratch-map" class="w-full overflow-x-auto">
        <svg
          ref="svgRef"
          class="w-full h-auto max-w-5xl mx-auto block"
          style="min-height: 300px;"
        ></svg>
      </div>

      <!-- Bouton partager -->
    <div class="flex justify-end">
      <button
        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
        @click="captureShareMap"
      >
        📤 Partager ma carte
      </button>
    </div>

      <!-- Barre de progression -->
      <div class="mt-8 space-y-2 text-sm text-gray-700 text-center sm:text-left">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <p class="font-medium">🌍 Vous avez visité {{ visitedPercent }}% du monde</p>
          <p class="text-gray-500">{{ visitedCount }} pays</p>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
          <div
            class="bg-green-400 h-3 rounded-full transition-all duration-500"
            :style="{ width: visitedPercent + '%' }"
          ></div>
        </div>
      </div>
    </div>


      <!-- Composant invisible à capturer -->
      <div class="absolute top-0 left-0 opacity-0 pointer-events-none -z-10">
        <div ref="shareRef">
          <!-- on passe nos info en composant caché parce qu'on est des noob on sait pas comment les recuperer sinon -->
          <ScratchMapShare 
            :visitedCountries="visitedCountries" 
            :userName="$page.props.auth.user.firstname" 
            :visitedCount="visitedCount"
            :visitedPercent="visitedPercent"/>
        </div>
      </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.responsive-svg {
  width: 100%;
  height: auto;
  display: block;
}
</style>
