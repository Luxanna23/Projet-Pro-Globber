<template>
    <div>
        <h1>Créer une annonce</h1>
        <p>Bienvenue sur la page de création d'annonces.</p>
    </div>
</template>

<!-- <template>
    <div class="container">
      <h1>Créer une annonce</h1>
  
      <form @submit.prevent="submitForm">
        <div>
          <label for="title">Titre</label>
          <input type="text" id="title" v-model="form.title" required />
        </div>
  
        <div>
          <label for="description">Description</label>
          <textarea id="description" v-model="form.description" required></textarea>
        </div>
  
        <div>
          <label for="price_per_night">Prix par nuit</label>
          <input type="number" id="price_per_night" v-model="form.price_per_night" required />
        </div>
  
        <div>
          <label for="address">Adresse</label>
          <input type="text" id="address" v-model="form.address" required />
        </div>
  
        <div>
          <label for="images">Images</label>
          <input type="file" id="images" @change="handleFileChange" multiple />
        </div>
  
        <button type="submit">Créer l'annonce</button>
      </form>
  
      <div v-if="successMessage" class="success">
        {{ successMessage }}
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useForm } from '@inertiajs/vue3';
  
  const form = useForm({
    title: '',
    description: '',
    price_per_night: '',
    address: '',
    images: []
  });
  
  const successMessage = ref(null);
  
  const handleFileChange = (event) => {
    form.images = event.target.files;
  };
  
  const submitForm = () => {
    form.post(route('annonces.store'), {
      onSuccess: () => {
        successMessage.value = 'Annonce créée avec succès.';
      },
      onError: () => {
        successMessage.value = 'Une erreur est survenue.';
      }
    });
  };
  </script>
  
  <style scoped>
  /* Style pour le formulaire */
  .container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
  }
  .success {
    margin-top: 20px;
    color: green;
  }
  </style> -->