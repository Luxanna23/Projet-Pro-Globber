<template>
  <section>
    <h2 :class="[
      'text-2xl font-semibold mb-4',
      color === 'blue' ? 'text-blue-600' :
      color === 'green' ? 'text-green-600' :
      color === 'red' ? 'text-red-600' : 'text-gray-700'
    ]">
      {{ title }}
    </h2>
    <div>
      <slot />
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  title: String,
  color: {
    type: String,
    default: 'gray',
  },
})
</script>