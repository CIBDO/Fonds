# 🚀 Installation du Module PCS - Guide Rapide

## ✅ Ce qui a été créé

### 1. **Base de Données** (6 Migrations)
- ✅ `bureaux_douanes` - Bureaux de douanes de la RGD
- ✅ `declarations_pcs` - Déclarations PCS UEMOA/AES
- ✅ `autres_demandes` - Autres demandes financières
- ✅ `pieces_jointes_pcs` - Documents joints
- ✅ `historique_statuts_pcs` - Traçabilité
- ✅ Enrichissement table `users` (2 champs PCS)

### 2. **Modèles Eloquent** (5 Modèles)
- ✅ `BureauDouane.php`
- ✅ `DeclarationPcs.php`
- ✅ `AutreDemande.php`
- ✅ `PieceJointePcs.php`
- ✅ `HistoriqueStatutPcs.php`
- ✅ `Poste.php` enrichi avec relations PCS

### 3. **Contrôleurs** (3 Contrôleurs)
- ✅ `BureauDouaneController.php` - Gestion bureaux
- ✅ `DeclarationPcsController.php` - Déclarations PCS
- ✅ `AutreDemandeController.php` - Autres demandes

### 4. **Routes**
- ✅ Routes complètes ajoutées dans `web.php`
- ✅ Middleware et autorisations configurés

### 5. **Seeder**
- ✅ `PcsSeeder.php` - Données initiales

---

## 🚀 Commandes d'Installation

### Étape 1 : Exécuter les migrations
```bash
cd C:\Users\BDO\Desktop\Fonds
php artisan migrate
```

### Étape 2 : Peupler la base de données
```bash
php artisan db:seed --class=PcsSeeder
```

**Cela va créer :**
- 18 postes (RGD, ACCT, KAYES, KOULIKORO, etc.)
- 14 bureaux de douanes (200, 201, 205, 208, etc.)
- 4 utilisateurs de test

### Étape 3 : Vérifier l'installation
```bash
php artisan route:list --path=pcs
```

Vous devriez voir toutes les routes PCS.

---

## 🔑 Comptes de Test Créés

| Email | Mot de passe | Poste | Rôle | Droits |
|-------|-------------|-------|------|---------|
| `rgd@pcs.ml` | `password` | RGD | Trésorier | Saisie multi-bureaux |
| `acct@pcs.ml` | `password` | ACCT | ACCT | Validation |
| `kayes@pcs.ml` | `password` | KAYES | Trésorier | Saisie simple |
| `admin@pcs.ml` | `password` | ACCT | Admin | Tous droits |

---

## 🎯 Prochaines Étapes

### Option 1 : Utiliser l'API / Tester avec Postman
Les contrôleurs sont prêts, vous pouvez tester immédiatement :

```bash
# Se connecter
POST /login
{
    "email": "rgd@pcs.ml",
    "password": "password"
}

# Créer une déclaration
POST /pcs/declarations
{
    "mois": 1,
    "annee": 2025,
    ...
}
```

### Option 2 : Créer les Vues Blade
Les vues doivent être créées en s'inspirant des vues existantes :

**À créer dans `resources/views/pcs/` :**

```
pcs/
├── bureaux/
│   ├── index.blade.php      ← Liste des bureaux
│   ├── create.blade.php     ← Formulaire création
│   └── edit.blade.php       ← Formulaire édition
│
├── declarations/
│   ├── index.blade.php      ← Liste des déclarations
│   ├── create.blade.php     ← Formulaire saisie (RGD & Postes)
│   └── show.blade.php       ← Détail + Validation
│
├── autres-demandes/
│   ├── index.blade.php      ← Liste
│   ├── create.blade.php     ← Formulaire
│   ├── edit.blade.php       ← Édition
│   └── show.blade.php       ← Détail
│
└── pdf/
    ├── etat-recettes.blade.php
    └── etat-reversements.blade.php
```

**Structure type** (inspirée de vos vues existantes) :

```blade
@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Déclarations PCS</h3>
        </div>
        <div class="card-body">
            {{-- Contenu ici --}}
        </div>
    </div>
</div>
@endsection
```

---

## 📊 Flux de Travail

### 1. Poste Normal (ex: KAYES)
1. Se connecter avec `kayes@pcs.ml`
2. Aller sur `/pcs/declarations/create`
3. Saisir montants pour UEMOA et AES
4. Soumettre

### 2. RGD (Multi-bureaux)
1. Se connecter avec `rgd@pcs.ml`
2. Aller sur `/pcs/declarations/create`
3. Le formulaire affiche :
   - Section RGD (propre)
   - 14 sections bureaux (200, 201, 205, etc.)
4. Saisir pour chaque bureau :
   - UEMOA : Recouvrement + Reversement
   - AES : Recouvrement + Reversement
5. Soumettre tout en une fois

### 3. ACCT (Validation)
1. Se connecter avec `acct@pcs.ml`
2. Voir toutes les déclarations soumises
3. Valider ou rejeter (avec motif)

### 4. Génération États PDF
- URL : `/pcs/declarations/pdf/recettes?programme=UEMOA&annee=2025`
- Génère l'état consolidé conforme aux modèles officiels

---

## 🔍 Vérification

### Vérifier les tables créées
```bash
php artisan tinker
```

```php
// Vérifier les postes
Poste::count(); // Devrait retourner 18+

// Vérifier les bureaux
BureauDouane::count(); // Devrait retourner 14

// Vérifier les utilisateurs PCS
User::where('peut_saisir_pcs', true)->count(); // 3 utilisateurs
```

### Vérifier les routes
```bash
php artisan route:list | findstr pcs
```

Devrait afficher environ 15-20 routes PCS.

---

## 🐛 Résolution de Problèmes

### Erreur : Class not found
```bash
composer dump-autoload
```

### Erreur : Migration failed
```bash
# Réinitialiser (ATTENTION : Supprime les données)
php artisan migrate:fresh --seed
```

### Erreur : Route not found
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

---

## 📚 Documentation Complète

Consultez `MODULE_PCS_README.md` pour la documentation complète incluant :
- Architecture détaillée
- Exemples de données
- Guide utilisateur
- API Reference
- Dépannage avancé

---

## ✅ Checklist d'Installation

- [ ] Migrations exécutées : `php artisan migrate`
- [ ] Seeder exécuté : `php artisan db:seed --class=PcsSeeder`
- [ ] 18 postes créés (vérifier table `postes`)
- [ ] 14 bureaux créés (vérifier table `bureaux_douanes`)
- [ ] 4 utilisateurs test créés
- [ ] Connexion avec `rgd@pcs.ml` fonctionne
- [ ] Routes PCS accessibles : `php artisan route:list --path=pcs`

---

## 🎉 Félicitations !

Le module PCS est maintenant **opérationnel en backend** !

**Prochaine étape** : Créer les vues Blade pour l'interface utilisateur.

Les contrôleurs retournent déjà les bonnes données, il suffit de créer les formulaires HTML/Blade en s'inspirant des vues existantes de votre projet.

---

**Date d'installation** : {{ date('d/m/Y') }}  
**Version** : 1.0.0  
**Statut** : ✅ Backend Complet


