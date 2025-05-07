<template>
    <Head title="Welcome" />

    <!-- ma nav ça-->
    <!-- <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white"
    >
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
            <Link
                v-if="$page.props.auth.user"
                :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                >Annonces</Link
            >

            <template v-else>
                <Link
                    :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Log in</Link
                >

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                    >Register</Link
                >
        </div> -->

    <!-- ma page -->

    <div>
        <GuestLayout>

        <!-- Hero Section -->
            <section class="hero-section py-16 md:py-24" :style="heroBackground">
                <div class="container mx-auto px-4 w-full">
                    <div class="max-w-2xl">
                        <h1 class="text-4xl md:text-5xl font-bold mb-6">
                            Trouvez votre prochaine aventure
                        </h1>
                        <p class="text-xl text-gray-700 mb-8">
                            Avec Globber, decouvrez le monde comme jamais auparavant.<br />
                            Explorez des logements uniques et vivez des expériences authentiques.
                        </p>
                        <div class="bg-white p-4 rounded-lg shadow-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Destination</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="ri-map-pin-line text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="text"
                                            class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none"
                                            placeholder="Où allez-vous?"
                                        />
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Dates</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="ri-calendar-line text-gray-400"
                                            ></i>
                                        </div>
                                        <input
                                            type="text"
                                            class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none"
                                            placeholder="Arrivée - Départ"
                                        />
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700"
                                        >Voyageurs</label
                                    >
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                        >
                                            <i
                                                class="ri-user-line text-gray-400"
                                            ></i>
                                        </div>
                                        <select
                                            class="pl-10 w-full py-3 border-none rounded bg-gray-50 focus:ring-2 focus:ring-primary focus:outline-none pr-8 appearance-none"
                                        >
                                            <option>1 voyageur</option>
                                            <option>2 voyageurs</option>
                                            <option>3 voyageurs</option>
                                            <option>4 voyageurs</option>
                                            <option>5+ voyageurs</option>
                                        </select>
                                        <div
                                            class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none"
                                        >
                                            <i
                                                class="ri-arrow-down-s-line text-gray-400"
                                            ></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button
                                    class="px-6 py-3 bg-primary text-white rounded-button flex items-center whitespace-nowrap"
                                >
                                    <i class="ri-search-line mr-2"></i>
                                    Rechercher
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <Footer />
        </GuestLayout>
    </div>
</template>

<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref, computed, onMounted, watch } from "vue";
import Footer from '@/Components/Footer.vue'
import heroImage from "@/Assets/welcome.png";

const darkMode = ref(false);

onMounted(() => {
    const savedTheme = localStorage.getItem("theme");
    if (
        savedTheme === "dark" ||
        (!savedTheme &&
            window.matchMedia("(prefers-color-scheme: dark)").matches)
    ) {
        darkMode.value = true;
    }
});

watch(darkMode, (val) => {
    if (val) {
        document.body.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        document.body.classList.remove("dark");
        localStorage.setItem("theme", "light");
    }
});

const heroBackground = computed(() => {
  return {
    backgroundImage: `linear-gradient(to right, rgba(255, 255, 255, 0.9) 40%, rgba(255, 255, 255, 0.7) 60%, rgba(255, 255, 255, 0) 100%), url('${heroImage}')`,
    backgroundSize: 'cover',
    backgroundPosition: 'center'
  }
})
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Pacifico&display=swap");
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
@import url("https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css");

body {
    font-family: "Poppins", sans-serif;
}
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}
.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 24px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}
input:checked + .toggle-slider {
    background-color: #3b82f6;
}
input:checked + .toggle-slider:before {
    transform: translateX(26px);
}
</style>
