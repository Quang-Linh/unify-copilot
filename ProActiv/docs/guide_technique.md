# Guide technique - ProActiv

## 1. Architecture

ProActiv est une application web développée en PHP orienté objet, suivant une architecture MVC (Modèle-Vue-Contrôleur) simplifiée.

- **`public/` :** Point d'entrée de l'application (`index.php`)
- **`src/` :** Code source de l'application (contrôleurs, services, etc.)
- **`templates/` :** Vues de l'application (fichiers PHP)
- **`assets/` :** Fichiers statiques (CSS, JS, images)
- **`config/` :** Fichiers de configuration
- **`docs/` :** Documentation du projet

## 2. Base de données

La base de données est gérée via PDO. Le schéma de la base de données est disponible dans `docs/schema.sql`.

- **`users` :** Table des utilisateurs
- **`documents` :** Table des documents (factures, devis)
- **`calculations` :** Table des calculs effectués
- **`forum_topics` :** Table des sujets du forum
- **`forum_posts` :** Table des réponses du forum

## 3. API REST

ProActiv expose une API REST pour les fonctionnalités principales.

- **Endpoint :** `/api`
- **Authentification :** Token JWT

### Exemples d'endpoints :

- `GET /api/stats` : Récupérer les statistiques globales
- `POST /api/calculate` : Effectuer un calcul
- `GET /api/forum/topics` : Lister les sujets du forum

## 4. Dépendances

- **PHP 8.1+**
- **MySQL 8.0+**
- **FPDF** (pour la génération de PDF)

## 5. Installation

1. Clonez le dépôt Git.
2. Configurez la base de données dans `config/app.php`.
3. Importez le schéma de la base de données (`docs/schema.sql`).
4. Lancez un serveur PHP local : `php -S localhost:8000 -t public`

## 6. Contribution

Pour contribuer au projet, veuillez suivre les étapes suivantes :

1. Forkez le projet.
2. Créez une nouvelle branche pour votre fonctionnalité.
3. Développez votre fonctionnalité.
4. Soumettez une pull request avec une description détaillée deion de request avec une description détaillée de vos changements.

