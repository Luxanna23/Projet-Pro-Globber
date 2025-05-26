<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  reservationId: Number,
  annonceTitle: String,
  show: Boolean,
})
const emit = defineEmits(['close'])

const form = useForm({
  content: '',
  rating: 0,
})

function setRating(n) {
  form.rating = n
}

function submit() {
  if (!form.content || !form.rating) return

  form.post(route('comment.store', props.reservationId), {
    onSuccess: () => {
      form.reset()
      emit('close')
    }
  })
}
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
      <h2 class="text-xl font-bold mb-2">Laisser un avis</h2>
      <p class="text-sm text-gray-600 mb-4">Annonce : {{ annonceTitle }}</p>

      <!-- Étoiles de notation -->
      <div class="flex items-center mb-4">
        <span
          v-for="n in 5"
          :key="n"
          @click="setRating(n)"
          class="cursor-pointer text-2xl"
        >
          <span :class="n <= form.rating ? 'text-yellow-400' : 'text-gray-300'">★</span>
        </span>
      </div>

      <!-- Champ de contenu -->
      <textarea
        v-model="form.content"
        rows="4"
        placeholder="Votre commentaire"
        class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring focus:ring-indigo-300"
      ></textarea>

      <!-- Boutons -->
      <div class="flex justify-end gap-4">
        <button
          @click="emit('close')"
          type="button"
          class="text-gray-500 text-sm"
        >
          Annuler
        </button>
        <button
          @click="submit"
          class="bg-indigo-600 text-white text-sm px-4 py-2 rounded hover:bg-indigo-700"
          :disabled="form.processing"
        >
          Envoyer
        </button>
      </div>
    </div>
  </div>
</template>