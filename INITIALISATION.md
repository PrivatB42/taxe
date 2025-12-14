# 🚀 Guide d'Initialisation de la Base de Données

## 📋 Étapes d'Initialisation

### Méthode 1 : Commande Artisan (RECOMMANDÉ)

```bash
php artisan db:init
```

Cette commande exécute automatiquement (dans l'ordre) :
- ✅ Les migrations
- ✅ L'initialisation des rôles
- ✅ L'initialisation des permissions
- ✅ La création des utilisateurs de test

**Alternative : Script PHP**
```bash
php init-database.php
```

### Méthode 2 : Commandes Artisan

#### 1. Exécuter les migrations

```bash
php artisan migrate
```

#### 2. Initialiser les données

**Option A : Utiliser le seeder principal (RECOMMANDÉ)**
```bash
php artisan db:seed
```
Exécute dans l'ordre : Rôles → Permissions → Utilisateurs

**Option B : Utiliser le seeder du module User**
```bash
php artisan db:seed --class="Modules\User\Database\Seeders\UserDatabaseSeeder"
```
Exécute dans l'ordre : Rôles → Permissions → Utilisateurs

**Option C : Exécuter les seeders individuellement (dans l'ordre)**

Dans PowerShell :
```powershell
# 1. D'abord les rôles
php artisan db:seed --class="Modules\User\Database\Seeders\RoleSeeder"

# 2. Ensuite les permissions
php artisan db:seed --class="Modules\User\Database\Seeders\PermissionSeeder"

# 3. Enfin les utilisateurs
php artisan db:seed --class="Modules\User\Database\Seeders\TestUsersSeeder"
```

Dans CMD ou Bash :
```bash
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\PermissionSeeder
php artisan db:seed --class=Modules\\User\\Database\\Seeders\\TestUsersSeeder
```

### 3. Vérifier les données créées

Après l'exécution, vous devriez avoir :

- ✅ **Rôles** : 5 rôles dans la table `user_roles` (admin, regisseur, agent_de_la_regie, caissier, superviseur)
- ✅ **Permissions** : Toutes les permissions définies dans `Constantes::PERMISSIONS` (y compris GERER_ROLES)
- ✅ **Rôles avec permissions** : Chaque rôle avec ses permissions attribuées dans `user_role_permissions`
- ✅ **5 Utilisateurs de test** : Un utilisateur pour chaque rôle

## 👥 Utilisateurs de Test Créés

| Rôle | Email | Téléphone | Mot de passe |
|------|-------|-----------|--------------|
| Admin | admin@test.com | 0100000001 | password123 |
| Régisseur | regisseur@test.com | 0100000002 | password123 |
| Agent de la Régie | agent@test.com | 0100000003 | password123 |
| Caissier | caissier@test.com | 0100000004 | password123 |
| Superviseur | superviseur@test.com | 0100000005 | password123 |

## 🔄 Réinitialisation Complète

Si vous voulez tout réinitialiser depuis le début :

```bash
php artisan migrate:fresh
php artisan db:seed
```

⚠️ **Attention** : Cette commande supprime toutes les données existantes !

## 🐛 Dépannage

### Aucune donnée n'apparaît

1. **Vérifiez que les migrations ont été exécutées** :
   ```bash
   php artisan migrate:status
   ```

2. **Régénérez l'autoload** :
   ```bash
   composer dump-autoload
   ```

3. **Exécutez les seeders manuellement** :
   ```bash
   php init-database.php
   ```
   ou
   ```bash
   php artisan db:seed --class="Modules\User\Database\Seeders\UserDatabaseSeeder"
   ```

4. **Vérifiez les erreurs dans les logs** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Erreurs de dépendances

Si vous obtenez des erreurs, assurez-vous que :
- Les migrations sont toutes exécutées
- La constante `COMMUNE_ID` est définie dans `Constantes.php`
- Les services sont correctement injectés
- L'autoload a été régénéré avec `composer dump-autoload`

## 📊 Vérification dans la Base de Données

Vous pouvez vérifier les données créées avec ces requêtes SQL :

```sql
-- Vérifier les permissions
SELECT * FROM user_permissions;

-- Vérifier les permissions par rôle
SELECT rp.role, COUNT(*) as nb_permissions 
FROM user_role_permissions rp 
GROUP BY rp.role;

-- Vérifier les utilisateurs
SELECT p.nom_complet, p.email, p.telephone, g.role, c.is_active
FROM user_personnes p
JOIN auth_comptes c ON c.personne_id = p.id
JOIN user_gestionnaires g ON g.personne_id = p.id;
```
