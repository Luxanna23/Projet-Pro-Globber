<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Information du Profile</h2>

            <p class="mt-1 text-sm text-gray-600">
                Modifie les informations de ton profile ici.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
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


            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Ton adresse mail n'est pas vérifiée.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Clicker ici pour re-envoyer l'email de vérification.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600"
                >
                    Un nouveau lien de vérification a été envoyé sur ton email.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Enregistrer</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Enregistré avec succès.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
