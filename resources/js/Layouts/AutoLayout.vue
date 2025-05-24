<script setup>
import { usePage } from '@inertiajs/vue3'
import GuestLayout from './GuestLayout.vue'
import AuthenticatedLayout from './AuthenticatedLayout.vue'

const page = usePage()
console.log('AutoLayout — user:', page.props.auth)

const user = page.props.auth?.user
</script>

<template>
  <component :is="user ? AuthenticatedLayout : GuestLayout">
    <slot />
  </component>
</template>