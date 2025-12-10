# 📚 Documentation - Système de Gestion des Taxes

## Table des matières

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Architecture](#architecture)
4. [Système d'Authentification](#système-dauthentification)
5. [Rôles et Permissions](#rôles-et-permissions)
6. [Modules](#modules)
7. [API Routes](#api-routes)
8. [Base de Données](#base-de-données)
9. [Utilisateurs de Test](#utilisateurs-de-test)

---

## Introduction

Ce système est une plateforme de gestion des taxes et contributions locales développée avec **Laravel 11** et une architecture modulaire. Elle permet de gérer les contribuables, les gestionnaires, les taxes et les paiements.

### Technologies utilisées

- **Backend** : Laravel 11
- **Base de données** : MySQL
- **Authentification** : Session Web + JWT (API)
- **Frontend** : Blade + Bootstrap 5
- **Architecture** : Modulaire (nwidart/laravel-modules)

---

## Installation

### Prérequis

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (optionnel, pour assets)

### Étapes d'installation

```bash
# 1. Cloner le projet
git clone [url-du-repo]
cd taxe

# 2. Installer les dépendances PHP
composer install

# 3. Copier le fichier d'environnement
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taxe_db
DB_USERNAME=root
DB_PASSWORD=

# 6. Exécuter les migrations
php artisan migrate

# 7. Créer les utilisateurs de test
php artisan db:seed --class=TestUsersSeeder

# 8. Démarrer le serveur
php artisan serve
```

---

## Architecture

### Structure des Modules

```
Modules/
├── Auth/           # Authentification et gestion des comptes
├── Dashboard/      # Tableau de bord
├── Entite/         # Entités (Communes, Activités, Taxes)
├── Paiement/       # Gestion des paiements et caisses
└── User/           # Utilisateurs (Contribuables, Gestionnaires)
```

### Structure d'un Module

```
Module/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
└── module.json
```

---

## Système d'Authentification

### Page de Connexion

**URL** : `/login`

Les utilisateurs peuvent se connecter avec :
- Email
- Numéro de téléphone
- Numéro de compte

### Middlewares

| Middleware | Description |
|------------|-------------|
| `auth.web` | Vérifie que l'utilisateur est authentifié |
| `role:admin` | Vérifie que l'utilisateur est administrateur |
| `role:superviseur` | Vérifie que l'utilisateur est superviseur |
| `role:gestionnaire` | Vérifie que l'utilisateur est gestionnaire |
| `guest` | Accessible uniquement si non connecté |

### Exemple d'utilisation

```php
// Route accessible uniquement aux admins
Route::middleware(['auth.web', 'role:admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});

// Route accessible aux superviseurs ET admins
Route::middleware(['auth.web', 'role:superviseur,admin'])->group(function () {
    Route::get('/gestionnaires', [GestionnaireController::class, 'index']);
});
```

---

## Rôles et Permissions

### Types de Comptes

| Rôle | Constante | Description |
|------|-----------|-------------|
| Administrateur | `COMPTE_ADMIN` | Accès complet à toutes les fonctionnalités |
| Superviseur | `COMPTE_SUPERVISEUR` | Gère les gestionnaires et suit leurs activités |
| Gestionnaire | `COMPTE_GESTIONNAIRE` | Gère uniquement les contribuables |
| Contribuable | `COMPTE_CONTRIBUABLE` | Accès limité à son propre compte |

### Permissions par Rôle

#### Gestionnaire
```php
[
    'contribuables.index',
    'contribuables.store',
    'contribuables.update',
    'contribuables.show',
    'contribuables.toggle-active',
    'contribuables-activites.*',
    'contribuables-parametres.*',
    'contribuables-taxes.*',
]
```

#### Superviseur
```php
[
    'gestionnaires.*',
    'activites-log.*',
    'dashboard.*',
]
```

#### Administrateur
```php
['*'] // Tous les droits
```

### Menus par Rôle

| Rôle | Menus visibles |
|------|----------------|
| Gestionnaire | Contribuables |
| Superviseur | Dashboard, Supervision (Gestionnaires, Activités) |
| Admin | Tous (Dashboard, Contribuables, Supervision, Configurations, Caisses) |

---

## Modules

### Module Auth

Gestion de l'authentification.

**Routes** :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/login` | Page de connexion |
| POST | `/login` | Traitement de la connexion |
| POST | `/logout` | Déconnexion |

**Modèles** :
- `Compte` : Comptes utilisateurs avec authentification

### Module User

Gestion des utilisateurs.

**Routes Contribuables** (Gestionnaire + Admin) :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/contribuables` | Liste des contribuables |
| POST | `/contribuables` | Créer un contribuable |
| GET | `/contribuables/{id}` | Détails d'un contribuable |
| PUT | `/contribuables/{id}` | Modifier un contribuable |
| DELETE | `/contribuables/{id}` | Supprimer un contribuable |

**Routes Supervision** (Superviseur + Admin) :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/supervision/gestionnaires` | Liste des gestionnaires |
| POST | `/supervision/gestionnaires` | Créer un gestionnaire |
| GET | `/supervision/activites-log` | Journal des activités |
| GET | `/supervision/activites-log/stats` | Statistiques des activités |

**Modèles** :
- `Personne` : Informations personnelles
- `Gestionnaire` : Gestionnaires du système
- `Contribuable` : Contribuables
- `ActiviteLog` : Journal des activités

### Module Dashboard

Tableau de bord avec statistiques.

**Routes** :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/` | Tableau de bord |
| GET | `/dashboard/stats` | API statistiques |

### Module Entite

Configuration des entités.

**Routes** (Admin uniquement) :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/configurations/activites` | Gestion des activités |
| GET | `/configurations/taxes` | Gestion des taxes |
| GET | `/configurations/activites-taxes` | Liaison activités-taxes |
| GET | `/configurations/taxes-constantes` | Constantes des taxes |

### Module Paiement

Gestion des paiements et caisses.

**Routes** (Admin uniquement) :
| Méthode | URL | Description |
|---------|-----|-------------|
| GET | `/caisses` | Liste des caisses |

---

## Base de Données

### Tables Principales

#### `auth_comptes`
```sql
- id (bigint)
- personne_id (foreignId)
- numero_compte (string, unique)
- password (string)
- type_compte (string) -- admin, superviseur, gestionnaire, contribuable
- is_active (boolean)
- timestamps
```

#### `user_personnes`
```sql
- id (bigint)
- nom_complet (string)
- slug (string, unique)
- email (string, unique, nullable)
- telephone (string, unique)
- photo (text, nullable)
- is_active (boolean)
- timestamps
```

#### `user_gestionnaires`
```sql
- id (bigint)
- personne_id (foreignId, unique)
- commune_id (foreignId)
- is_active (boolean)
- timestamps
```

#### `user_contribuables`
```sql
- id (bigint)
- personne_id (foreignId, unique)
- matricule (string, unique)
- commune_id (foreignId)
- is_active (boolean)
- timestamps
```

#### `user_activites_log`
```sql
- id (bigint)
- gestionnaire_id (foreignId)
- action (string) -- create, update, delete, toggle
- model_type (string)
- model_id (bigint, nullable)
- description (string)
- old_values (json, nullable)
- new_values (json, nullable)
- ip_address (string, nullable)
- user_agent (string, nullable)
- timestamps
```

---

## Utilisateurs de Test

### Créer les utilisateurs

```bash
php artisan db:seed --class=TestUsersSeeder
```

### Identifiants

| Rôle | Email | Mot de passe | N° Compte | Téléphone |
|------|-------|--------------|-----------|-----------|
| **Admin** | admin@taxe.local | admin123 | ADM-2024-0001 | 0700000001 |
| **Superviseur** | superviseur@taxe.local | superviseur123 | SUP-2024-0001 | 0700000002 |
| **Gestionnaire** | gestionnaire@taxe.local | gestionnaire123 | GES-2024-0001 | 0700000003 |

---

## Helpers et Utilitaires

### Classe Constantes

```php
use App\Helpers\Constantes;

// Types de comptes
Constantes::COMPTE_ADMIN
Constantes::COMPTE_SUPERVISEUR
Constantes::COMPTE_GESTIONNAIRE
Constantes::COMPTE_CONTRIBUABLE

// Vérifier une permission
Constantes::hasPermission($typeCompte, 'contribuables.index');

// Vérifier l'accès à un menu
Constantes::canAccessMenu($typeCompte, 'supervision');
```

### Service ActiviteLogService

```php
use Modules\User\Services\ActiviteLogService;

// Logger une création
ActiviteLogService::logCreate('Contribuable', $id, 'Création du contribuable X');

// Logger une modification
ActiviteLogService::logUpdate('Contribuable', $id, 'Modification', $oldValues, $newValues);

// Logger une suppression
ActiviteLogService::logDelete('Contribuable', $id, 'Suppression du contribuable X');

// Obtenir les statistiques
$stats = ActiviteLogService::getStats($gestionnaireId, $dateDebut, $dateFin);
```

---

## Commandes Artisan Utiles

```bash
# Vider tous les caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Lister les routes
php artisan route:list

# Exécuter les migrations
php artisan migrate

# Rollback des migrations
php artisan migrate:rollback

# Créer un nouveau module
php artisan module:make NomDuModule
```

---

## Support

Pour toute question ou problème, veuillez contacter l'équipe de développement.

---

*Documentation générée le {{ date('d/m/Y') }}*

