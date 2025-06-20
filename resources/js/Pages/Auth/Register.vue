<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const rgpdAccepted = ref(false)
const showRgpdModal = ref(false)

const submit = () => {
    if (!rgpdAccepted.value) {
        alert("Veuillez accepter les conditions RGPD pour vous inscrire.");
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

</script>

<template>
    <GuestLayout>
        <Head title="Inscription" />

        <form @submit.prevent="submit" class="max-w-xl w-full mx-auto mt-12 p-6 bg-white shadow-lg rounded-xl flex flex-col gap-6">
            <div>
                <InputLabel for="firstname" value="Prenom" />

                <TextInput
                    id="firstname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.firstname"
                    required
                    autofocus
                    autocomplete="firstname"
                />

                <InputError class="mt-2" :message="form.errors.firstname" />
            </div>

            <div>
                <InputLabel for="lastname" value="Nom" />

                <TextInput
                    id="lastname"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.lastname"
                    required
                    autofocus
                    autocomplete="lastname"
                />

                <InputError class="mt-2" :message="form.errors.lastname" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div> <!-- tel -->
                <InputLabel for="phone" value="Numero de téléphone" />

                <TextInput
                    id="phone"
                    type="tel"
                    placeholder="06 12 34 56 78"
                    pattern="^(\+33|0)[1-9](\d{2}){4}$"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autofocus
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div> <!-- addresse -->
                <InputLabel for="address" value="addresse" />

                <TextInput
                    id="address"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.address"
                    required
                    autofocus
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

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmation du mot de passe" />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="text-sm text-gray-700 flex items-start gap-2">
                <input type="checkbox" id="rgpd" v-model="rgpdAccepted" required class="mt-1">
                <label for="rgpd">
                    J’accepte les
                    <button type="button" class="text-blue-600 underline" @click="showRgpdModal = true">
                        conditions d'utilisation et la politique de confidentialité
                    </button>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Déjà inscrit ?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Inscription
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>


    <div v-if="showRgpdModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg max-w-xl w-full shadow-lg overflow-y-auto max-h-[80vh]">
            <h2 class="text-xl font-semibold mb-4">Conditions RGPD</h2>
            <p class="text-sm text-gray-700">
            En vous inscrivant, vous acceptez que vos données soient utilisées exclusivement dans le cadre de l’application. 
            Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d’un droit d’accès, de 
            rectification et de suppression de vos informations personnelles.
            </p>
            <p class="text-sm text-gray-700 mt-4">
            Vos données sont stockées de manière sécurisée et ne seront jamais transmises à des tiers sans votre consentement.
            Vous pouvez supprimer votre compte à tout moment depuis votre espace personnel.
            </p>
            <div class="mt-6 text-right">
            <button @click="showRgpdModal = false" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700">
                Fermer
            </button>
            </div>
        </div>
    </div>

</template>
