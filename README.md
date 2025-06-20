## Projet App Voyage et Location : Globber!

Projet d'application Web de voyage : Globber.
Une application permettant de reserver des appartements et voyager a travers le monde en Laravel et Vue3 avec Inertia.

- Systeme d'authentification.
- Systeme de publication et scroll d'annonces en ligne.
- Systeme de reservations via des disponibilités.
- Systeme de calendrier pour gerer les disponibilités des annonces avec les reservations passées.
- Gestion des annonces coté propriétaire pour les accepter ou refuser.
- Systeme de messagerie entre proprietaire et locataire une fois la reservation acceptée.
- Systeme de paiement.
- Systeme de ScrathMap qui se remplie avec les voyages effectués et une page rewards.

## Installation et démarrage du projet



### Avec Docker 

1. **Cloner le projet**
   ```bash
   git clone <repository-url>
   cd Projet-Pro-Globber
   ```

2. **Configuration du fichier d'environnement**
   ```bash
   cp .env.example .env
   ```

3. **Construction et démarrage des conteneurs Docker**
   ```bash
   docker-compose build
   docker-compose up -d
   ```

4. **Installation des dépendances et préparation de l'application**
   ```bash
   # Génération de la clé d'application
   docker-compose exec php php artisan key:generate
   
   # Lancement des migrations pour créer les tables dans la base de données
   docker-compose exec php php artisan migrate
   
   # Remplissage de la base de données avec des données de test 
   docker-compose exec php php artisan db:seed
   
   # SI BESOIN DE REFRESH A CAUSE DES SEEDERS (non applicable en production)
   docker-compose exec php php artisan migrate:fresh --seed

   # Création des liens symboliques pour le stockage
   docker-compose exec php php artisan storage:link
   ```

5. **Accès à l'application**
   - Backend et Frontend: http://localhost


## Commandes utiles

- **Lancer les tests**
  ```bash
  # Avec Docker
  docker-compose exec php php artisan test
 

- **Vider le cache**
  ```bash
  # Avec Docker
  docker-compose exec php php artisan cache:clear
  

- **Arrêter les conteneurs Docker**
  ```bash
  # Avec Docker
  docker-compose down

- **compile les fichiers Vue**
npm run build           
php artisan config:cache
php artisan route:cache
php artisan view:cache