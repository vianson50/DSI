# Site Web DSI

Site vitrine professionnel avec back-office, construit avec **PHP 8+, MySQL, Tailwind CSS et JavaScript** (vanilla, sans dépendance Node).

## Structure du projet

```
├── index.php              # Page d'accueil (héro, services, à propos, stats)
├── services.php           # Page des services (depuis MySQL)
├── contact.php            # Formulaire de contact (enregistré en MySQL)
├── database.sql           # Script de création de la base + données initiales
├── includes/
│   ├── config.php         # Connexion PDO (à configurer)
│   ├── functions.php      # Fonctions utilitaires (sécurité, requêtes)
│   ├── header.php         # En-tête + navigation
│   └── footer.php         # Pied de page
├── admin/                 # Espace administrateur protégé
│   ├── login.php          # Connexion (sessions + CSRF)
│   ├── index.php          # Tableau de bord (statistiques)
│   ├── services.php       # CRUD des services
│   ├── messages.php       # Gestion des messages de contact
│   └── logout.php
└── assets/
    ├── img/               # Logo et images des services
    ├── css/style.css      # Styles personnalisés (animations)
    └── js/main.js         # Interactions (menu, compteurs, validation)
```

## Installation

1. **Base de données** : importez `database.sql` via phpMyAdmin, ou en ligne de commande :
   ```bash
   mysql -u root -p < database.sql
   ```

2. **Configuration** : modifiez `includes/config.php` si vos identifiants MySQL
   diffèrent (hôte, nom de base, utilisateur, mot de passe).

3. **Lancer le site** (serveur de développement PHP) :
   ```bash
   php -S localhost:8080
   ```
   Puis ouvrez http://localhost:8080
   > En production, utilisez Apache ou Nginx avec PHP-FPM.

## Espace administrateur

- URL : `http://localhost:8080/admin/login.php`
- Identifiants par défaut : **admin / admin123**
- ⚠️ **Changez ce mot de passe** après la première connexion :
  ```bash
  php -r "echo password_hash('VOTRE_NOUVEAU_MOT_DE_PASSE', PASSWORD_DEFAULT);"
  ```
  Puis mettez à jour la table `admins` dans MySQL.

## Fonctionnalités

| Public | Admin |
|---|---|
| Accueil animé (compteurs, apparitions au défilement) | Tableau de bord avec statistiques |
| Services affichés dynamiquement depuis MySQL | Ajout / modification / suppression de services |
| Formulaire de contact sécurisé (validation JS + PHP) | Lecture, réponse, marquage lu/non lu, suppression des messages |
| Design responsive (mobile, tablette, bureau) | Protection par sessions et jetons CSRF |

## Sécurité intégrée

- Requêtes préparées PDO (protection contre l'injection SQL)
- Échappement systématique des sorties (protection XSS)
- Jetons CSRF sur les formulaires sensibles
- Mots de passe hachés avec `password_hash()`
- Honeypot anti-spam sur le formulaire de contact
