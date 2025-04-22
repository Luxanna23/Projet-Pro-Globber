<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    description: '',
    address: '',
    postal_code: '',
    city: '',
    country: '',
    price_per_night: '',
    images: null,
});

const handleImageUpload = (e) => {
    form.images = e.target.files;
};

const submit = () => {
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


            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Publier
                </PrimaryButton>
            </div>
            
      </form>
  </AuthenticatedLayout>
</template>