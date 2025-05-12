<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, nextTick } from 'vue'

const props = defineProps({
  reservations: Array,
  userId: Number
})

const activeReservation = ref(null)
const form = useForm({ content: '' })

const openChat = (reservation) => {
  activeReservation.value = reservation
  nextTick(() => {
    const el = document.getElementById('chat-messages')
    if (el) el.scrollTop = el.scrollHeight
  })
}

const submit = () => {
  if (!form.content.trim() || !activeReservation.value) return;

  const content = form.content;

  form.post(route('messages.store', activeReservation.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();

      if (!Array.isArray(activeReservation.value.messages)) {
        activeReservation.value.messages = [];
      }

      // un nouveau tableau pour déclencher la réactivité
      activeReservation.value.messages = [
        ...activeReservation.value.messages,
        {
          id: Date.now(),
          sender_id: props.userId,
          content,
          created_at: new Date().toISOString(),
          sender: { name: 'Vous' }, // optionnel si tu veux afficher l'auteur
        }
      ];

      console.log("✅ Message ajouté localement :", activeReservation.value.messages);

      scrollToBottom();
    }
  });
};

</script>

<template>
  <AuthenticatedLayout>
    <Head title="Messagerie" />

    <div class="max-w-7xl mx-auto p-4 grid grid-cols-1 md:grid-cols-3 gap-6 h-[80vh]">
      <!-- bloc de messages -->
      <div class="bg-white border rounded-xl p-4 overflow-y-auto md:col-span-1">
        <h2 class="text-xl font-semibold mb-4">Conversations</h2>
        <div
          v-for="reservation in reservations"
          :key="reservation.id"
          @click="openChat(reservation)"
          class="p-3 mb-2 rounded-lg cursor-pointer hover:bg-gray-100"
          :class="{ 'bg-indigo-50': activeReservation?.id === reservation.id }"
        >
          <h3 class="font-semibold text-sm">{{ reservation.annonce?.user?.name }}</h3>
          <p class="text-xs text-gray-500">{{ reservation.annonce?.title }}</p>
        </div>
      </div>

      <!-- messages -->
      <div v-if="activeReservation" class="bg-white border rounded-xl p-4 flex flex-col md:col-span-2">
        <h2 class="text-lg font-semibold mb-2">
          Conversation avec <span class="text-indigo-600">{{ activeReservation.annonce?.title }}</span>
        </h2>

        <div id="chat-messages" class="flex-1 overflow-y-auto space-y-3 py-4 border-t border-b my-2">
          <div
            v-for="msg in activeReservation.messages"
            :key="msg.id"
            :class="msg.sender_id === userId ? 'text-right' : 'text-left'"
          >
            <div
              :class="[
                'inline-block px-4 py-2 rounded-lg text-sm max-w-[70%]',
                msg.sender_id === userId ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'
              ]"
            >
              {{ msg.content }}
            </div>
            <div class="text-xs text-gray-500 mt-1">
              {{ new Date(msg.created_at).toLocaleString() }}
            </div>
          </div>
        </div>

        <form @submit.prevent="submit" class="flex gap-4 mt-4">
          <input
            v-model="form.content"
            type="text"
            placeholder="Écrivez un message..."
            class="flex-1 border rounded-lg px-4 py-2 text-sm"
          />
          <button
            type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm"
            :disabled="form.processing"
          >
            Envoyer
          </button>
        </form>
      </div>

      <div v-else class="md:col-span-2 flex items-center justify-center text-gray-400 italic">
        Sélectionnez une conversation pour commencer à discuter
      </div>
    </div>
  </AuthenticatedLayout>
</template>