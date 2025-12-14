# 📋 Ordre d'Exécution des Seeders

## ⚠️ Ordre Important

Les seeders **DOIVENT** être exécutés dans cet ordre précis :

### 1️⃣ RoleSeeder (EN PREMIER)
```bash
php artisan db:seed --class="Modules\User\Database\Seeders\RoleSeeder"
```

**Pourquoi en premier ?**
- Les rôles doivent exister dans la table `user_roles` avant d'être utilisés
- Les permissions et utilisateurs référencent les codes de rôles

**Crée :**
- 5 rôles par défaut dans `user_roles`

---

### 2️⃣ PermissionSeeder (EN DEUXIÈME)
```bash
php artisan db:seed --class="Modules\User\Database\Seeders\PermissionSeeder"
```

**Pourquoi en deuxième ?**
- Les permissions doivent exister avant d'être attribuées aux rôles
- Les rôles doivent exister pour recevoir les permissions

**Crée :**
- Toutes les permissions dans `user_permissions`
- Les attributions de permissions aux rôles dans `user_role_permissions`

**Vérifie :**
- Que les rôles existent dans la table avant d'attribuer les permissions

---

### 3️⃣ TestUsersSeeder (EN DERNIER)
```bash
php artisan db:seed --class="Modules\User\Database\Seeders\TestUsersSeeder"
```

**Pourquoi en dernier ?**
- Les utilisateurs ont besoin que les rôles existent dans la table
- Les utilisateurs sont créés avec un rôle assigné

**Crée :**
- 5 utilisateurs de test (un par rôle)
- Les comptes associés dans `auth_comptes`
- Les gestionnaires dans `user_gestionnaires`

**Vérifie :**
- Que tous les rôles requis existent avant de créer les utilisateurs
- Affiche une erreur claire si des rôles manquent

---

## 🚀 Commandes Recommandées

### Option 1 : Tout en une fois (RECOMMANDÉ)
```bash
php artisan db:seed
```
ou
```bash
php artisan db:init
```

### Option 2 : Seeder du module User
```bash
php artisan db:seed --class="Modules\User\Database\Seeders\UserDatabaseSeeder"
```

### Option 3 : Individuellement (si nécessaire)
```bash
# 1. Rôles
php artisan db:seed --class="Modules\User\Database\Seeders\RoleSeeder"

# 2. Permissions
php artisan db:seed --class="Modules\User\Database\Seeders\PermissionSeeder"

# 3. Utilisateurs
php artisan db:seed --class="Modules\User\Database\Seeders\TestUsersSeeder"
```

## 🔄 Réinitialisation Complète

```bash
php artisan migrate:fresh
php artisan db:seed
```

⚠️ **Attention** : Cette commande supprime toutes les données existantes !

## ✅ Vérification

Après l'exécution, vérifiez que vous avez :

1. **Rôles** : `SELECT * FROM user_roles;` → 5 rôles
2. **Permissions** : `SELECT * FROM user_permissions;` → 17 permissions
3. **Permissions par rôle** : `SELECT role, COUNT(*) FROM user_role_permissions GROUP BY role;`
4. **Utilisateurs** : `SELECT * FROM user_personnes p JOIN user_gestionnaires g ON g.personne_id = p.id;` → 5 utilisateurs

