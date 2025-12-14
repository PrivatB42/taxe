# Configuration du Système de Login et Utilisateurs de Test

## 🚀 Installation

### 1. Exécuter les migrations

```bash
php artisan migrate
```

### 2. Initialiser les permissions

```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionSeeder
```

### 3. Créer les utilisateurs de test

```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\TestUsersSeeder
```

## 👥 Utilisateurs de Test

Tous les utilisateurs de test ont le mot de passe : **`password123`**

| Rôle | Email | Téléphone | Nom |
|------|-------|-----------|-----|
| **Admin** | admin@test.com | 0100000001 | Administrateur Système |
| **Régisseur** | regisseur@test.com | 0100000002 | Jean Régisseur |
| **Agent de la Régie** | agent@test.com | 0100000003 | Marie Agent |
| **Caissier** | caissier@test.com | 0100000004 | Pierre Caissier |
| **Superviseur** | superviseur@test.com | 0100000005 | Sophie Superviseur |

## 🔐 Connexion

1. Accédez à la page de login : `/auth/login`
2. Utilisez l'email ou le téléphone comme identifiant
3. Entrez le mot de passe : `password123`

## 📋 Permissions par Rôle

### Admin
- ✅ Toutes les permissions du système

### Régisseur
- ✅ Tous les droits des agents de la Régie
- ✅ Gestion des utilisateurs de l'app
- ✅ Gestion des caisses
- ✅ Tableau de bord et reportings

### Agent de la Régie
- ✅ Création et gestion des taxes
- ✅ Création et gestion des contribuables
- ✅ Création d'activités taxables
- ✅ Création de caisses
- ✅ Création et gestion des caissiers

### Caissier
- ✅ Ouverture et fermeture de caisse
- ✅ Encaissement
- ✅ Impression reçu de paiement

### Superviseur
- ✅ Tableau de bord et reporting

## 🎨 Fonctionnalités

- **Page de login moderne** avec design gradient
- **Comptes de test** affichés directement sur la page de login
- **Gestion des permissions** par rôle
- **Gestion des utilisateurs** avec attribution de rôles
- **Interface moderne** et responsive

## 🔧 Commandes Utiles

### Réinitialiser les permissions
```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionSeeder
```

### Recréer les utilisateurs de test
```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\TestUsersSeeder
```

### Réinitialiser complètement
```bash
php artisan migrate:fresh
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionSeeder
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\TestUsersSeeder
```

