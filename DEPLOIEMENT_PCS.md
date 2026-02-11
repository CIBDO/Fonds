# Résolution : Vues PCS non affichées en production

## Causes fréquentes et solutions

### 1. Permissions sur storage et bootstrap/cache (prioritaire)

Laravel compile les vues Blade dans `storage/framework/views/`. Si ce dossier n’est pas accessible en écriture, les vues échouent souvent sans message clair.

**À exécuter sur le serveur de production :**

```bash
# Permissions d’écriture pour le web server
chmod -R 775 storage bootstrap/cache

# Propriétaire si Apache/nginx (adapter www-data selon votre configuration)
chown -R www-data:www-data storage bootstrap/cache
```

Sous Windows (WSL) ou hébergement mutualisé, adapter selon la configuration.

---

### 2. Cache obsolète

Les caches (config, routes, vues) peuvent provoquer des erreurs si générés dans un environnement différent ou avant la mise en place des droits d’écriture.

**À exécuter après déploiement :**

```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Puis, si vous voulez recréer les caches :

```bash
php artisan config:cache
php artisan route:cache
```

---

### 3. Sensibilité à la casse (Linux)

En production (Linux), les chemins de fichiers sont sensibles à la casse. Les vues PCS doivent correspondre exactement à la casse suivante :

| Chemin attendu | Résultat |
|----------------|----------|
| `resources/views/pcs/` | correct (tout en minuscules) |
| `resources/views/PCS/` | incorrect en production |

Contrôler sur le serveur que les dossiers sont bien en minuscules :

```bash
ls -la resources/views/ | grep -i pcs
```

Si le dossier est `PCS` en majuscules, le renommer :

```bash
mv resources/views/PCS resources/views/pcs
```

---

### 4. Erreurs masquées (APP_DEBUG)

Avec `APP_DEBUG=false`, les erreurs ne sont pas affichées et on obtient une page blanche ou un 500.

Pour diagnostiquer temporairement, dans `.env` :

```env
APP_DEBUG=true
APP_ENV=local
```

Redémarrer l’application, reproduire le problème, puis remettre :

```env
APP_DEBUG=false
APP_ENV=production
```

---

### 5. Vérifier que les vues sont bien déployées

Les chemins suivants doivent exister sur le serveur :

```bash
resources/views/pcs/declarations/
resources/views/pcs/autres-demandes/
resources/views/pcs/etats-consolides/
resources/views/pcs/destockages/
resources/views/pcs/bureaux/
resources/views/pcs/pdf/
```

Si vous utilisez Git :

```bash
git status
git ls-files resources/views/pcs/
```

Pour s’assurer que tout est bien versionné et déployé.

---

### 6. Composer et autoload en production

Vérifier que l’autoload des contrôleurs PCS fonctionne :

```bash
composer dump-autoload -o
```

Le dossier `app/Http/Controllers/PCS/` doit exister et la casse (PCS en majuscules) doit être respectée sur Linux.

---

### 7. Checklist de déploiement recommandée

1. `git pull` ou copie des fichiers
2. `composer install --no-dev --optimize-autoloader`
3. Vérifier/créer `.env` et `APP_KEY`
4. `chmod -R 775 storage bootstrap/cache`
5. `chown` approprié pour `storage` et `bootstrap/cache`
6. `php artisan migrate --force` si nécessaire
7. `php artisan optimize:clear`
8. `php artisan config:cache` et `php artisan route:cache` si vous utilisez les caches

---

### 8. Diagnostic rapide (test de rendu des vues)

Pour vérifier que Laravel peut rendre une vue PCS :

```bash
php artisan tinker
>>> view('pcs.declarations.index', ['declarations' => collect(), 'totalDeclarations' => 0])->render();
```

Si une erreur apparaît, elle indiquera la cause (fichier manquant, erreur dans la vue, etc.).
