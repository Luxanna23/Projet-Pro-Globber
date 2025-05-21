<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
const startDate = ref(null)
const endDate = ref(null)
const disponibilites = ref([])

const form = useForm({
    title: '',
    description: '',
    address: '',
    postal_code: '',
    city: '',
    country: '',
    price_per_night: '',
    images: null,
    disponibilites: [],
});

function formatDate(date) {
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const handleImageUpload = (e) => {
    form.images = e.target.files;
};

const addDisponibilite = () => {
  if (startDate.value && endDate.value && new Date(endDate.value) >= new Date(startDate.value)) {
    disponibilites.value.push({
      start: startDate.value,
      end: endDate.value,
    })
    startDate.value = null
    endDate.value = null
  } else {
    alert("La date de fin doit être après la date de début")
  }
}

const removeDisponibilite = (index) => {
  disponibilites.value.splice(index, 1)
}

const submit = () => {
    form.disponibilites = disponibilites.value.map(p => ({
        start: formatDate(p.start),
        end: formatDate(p.end),
    }));
    form.post(route('annonces.store'), {
        forceFormData: true,
        onFinish: () => form.reset(),
    });
};

</script>

<template>
  <AuthenticatedLayout>
        <Head title="Créer une annonce" />

        <form @submit.prevent="submit" class="max-w-xl w-full mx-auto mt-12 p-6 bg-white shadow-lg rounded-xl flex flex-col gap-6">
              <!-- POUR DL L'IMAGE -->
              <div>
                <InputLabel for="images" value="Images" />
                <input
                    id="images"
                    type="file"
                    class="mt-1 block w-full"
                    multiple
                    @change="handleImageUpload"
                />
                <InputError class="mt-2" :message="form.errors.images" />
            </div>


            <div>
                <InputLabel for="title" value="Titre" />

                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    required
                    autofocus
                    autocomplete="title"
                />

                <InputError class="mt-2" :message="form.errors.title" />
            </div>

            <div>
                <InputLabel for="description" value="Description" />

                <TextInput
                    id="description"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.description"
                    required
                    autofocus
                    autocomplete="description"
                />

                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <div> <!-- addresse -->
                <InputLabel for="address" value="Addresse" />

                <TextInput
                    id="address"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address"
                    required
                    autocomplete="address"
                />

                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <div> <!-- code postale -->
                <InputLabel for="postal_code" value="Code Postal" />

                <TextInput
                    id="postal_code"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.postal_code"
                    required
                    autofocus
                    autocomplete="postal_code"
                />

                <InputError class="mt-2" :message="form.errors.postal_code" />
            </div>

            <div>  <!-- ville -->
                <InputLabel for="city" value="Ville" />

                <TextInput
                    id="city"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.city"
                    required
                    autofocus
                    autocomplete="city"
                />

                <InputError class="mt-2" :message="form.errors.city" />
            </div>

            <div>  <!-- Pays -->
                <InputLabel for="country" value="Pays" />

                <TextInput
                    id="country"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.country"
                    required
                    autofocus
                    autocomplete="country"
                />

                <InputError class="mt-2" :message="form.errors.country" />
            </div>

            <div>  <!-- price_per_night -->
                <InputLabel for="price_per_night" value="Prix par nuit" />

                <TextInput
                    id="price_per_night"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.price_per_night"
                    required
                    autofocus
                    autocomplete="price_per_night"
                />

                <InputError class="mt-2" :message="form.errors.price_per_night" />
            </div>

            <!-- calendrier -->
            <div class="space-y-4">
                <InputLabel value="Selectionnez les disponibilités" class="text-base font-semibold" />

                <div class="grid sm:grid-cols-3 gap-4 items-end">
                    <div>
                    <label class="block text-sm text-gray-700 mb-1">Date de début</label>
                    <Datepicker 
                        v-model="startDate" 
                        :enable-time-picker="false" 
                        auto-apply
                        :format="'dd/MM/yyyy'"
                        placeholder="Debut"
                        class="w-full rounded border-gray-300 shadow-sm" />
                    </div>

                    <div>
                    <label class="block text-sm text-gray-700 mb-1">Date de fin</label>
                    <Datepicker 
                        v-model="endDate" 
                        :enable-time-picker="false" 
                        auto-apply
                        :format="'dd/MM/yyyy'"
                        placeholder="Fin"
                        class="w-full rounded border-gray-300 shadow-sm" />
                    </div>

                    <div class="pt-2">
                    <button
                        type="button"
                        @click="addDisponibilite"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full"
                    >
                        Ajouter cette période
                    </button>
                    </div>
                </div>

                <div v-if="disponibilites?.length" class="mt-2 border rounded-lg divide-y overflow-hidden">
                    <div
                    v-for="(periode, index) in disponibilites"
                    :key="index"
                    class="flex justify-between items-center px-4 py-2 bg-gray-50 hover:bg-gray-100"
                    >
                    <span class="text-sm text-gray-800">
                        📅 {{ new Date(periode.start).toLocaleDateString() }} → {{ new Date(periode.end).toLocaleDateString() }}
                    </span>
                    <button
                        type="button"
                        @click="removeDisponibilite(index)"
                        class="text-red-500 text-xs font-medium hover:underline"
                    >
                        Supprimer
                    </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Publier
                </PrimaryButton>
            </div>
            
      </form>
  </AuthenticatedLayout>
</template>

<style scoped>
  .vue-datepicker {
    width: 100%;
  }
  .vue-datepicker__calendar {
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }
  .vue-datepicker__day--selected {
    background-color: #3b82f6;
    color: #fff;
  }
</style>