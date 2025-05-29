<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const showingNavigationDropdown = ref(false);

const hasUnreadMessages = ref(false)

onMounted(async () => {
  setInterval(async () => {
    try {
        const response = await axios.get(route('messages.unreadCount'))
        hasUnreadMessages.value = response.data.has_unread
    } catch (error) {
        console.error('Erreur polling badge message', error)
    }
    }, 10000)
})
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- La page d'acceuil -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                                <NavLink :href="route('annonces.index')" :active="route().current('annonces.index')">
                                    Annonces
                                </NavLink>
                            </div>

                            <!-- Publier une annonce -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                                <NavLink :href="route('annonces.create')" :active="route().current('annonces.create')">
                                    Publier une annonce
                                </NavLink>
                            </div>

                             <!-- la page rechercher
                             <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                                <NavLink :href="route('')" :active="route().current('')">
                                    Rechercher
                                </NavLink>
                            </div> -->

                        </div>

                      
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                              <!-- Coté propriétaire -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                                <NavLink :href="route('owner.reservations')" :active="route().current('owner.reservations')">
                                    Coté propriétaire
                                </NavLink>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('profile.reservations')"> Mes réservations </DropdownLink>
                                            <!-- pcq les messages ont des notif -->
                                        <DropdownLink
                                            :href="route('messages.inbox')"
                                            class="flex items-center gap-1 relative"
                                            >
                                            <span>Mes messages</span>
                                            <span
                                                v-if="hasUnreadMessages"
                                                class="w-2.5 h-2.5 bg-red-500 rounded-full"
                                            ></span>
                                        </DropdownLink>
                                        <DropdownLink :href="route('profile.rewards')">Récompenses</DropdownLink>
                                        <DropdownLink :href="route('profile.scratchmap')">My Map</DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('register')" :active="route().current('register')">
                            Register
                        </ResponsiveNavLink>
                    </div>

                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('login')" :active="route().current('login')">
                            Login
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.reservations')"> Mes réservations </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('messages.inbox')"> Mes messages </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.rewards')">Récompenses</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.scratchmap')">My Map</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <!-- Bottom Navigation (mobile only) -->
            <div class="fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 sm:hidden z-50">
                <div class="flex justify-around py-2 text-xs text-gray-600">
                    <Link href="/annonces" class="flex flex-col items-center">
                    <i class="ri-search-line text-lg"></i>
                    <span>Explorer</span>
                    </Link>
                    <Link href="/profile/scratchmap" class="flex flex-col items-center">
                    <i class="ri-map-line text-lg"></i>
                    <span>My map</span>
                    </Link>
                    <Link href="/annonces/create" class="flex flex-col items-center">
                    <i class="ri-heart-line text-lg"></i>
                    <span>Poster</span>
                    </Link>
                    <Link href="/messages" class="flex flex-col items-center relative">
                    <i class="ri-chat-3-line text-lg"></i>
                    <span>Messages</span>
                    <span
                        v-if="hasUnreadMessages"
                        class="absolute top-0 right-1 w-2.5 h-2.5 bg-red-500 rounded-full"
                    ></span>
                    </Link>
                    <Link href="/profile" class="flex flex-col items-center">
                    <i class="ri-user-line text-lg"></i>
                    <span>Profil</span>
                    </Link>
                </div>
            </div>
            
        </div>
    </div>
</template>
