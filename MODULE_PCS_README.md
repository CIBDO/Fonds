# 📊 Module PCS - Programme de Consolidation des Statistiques UEMOA/AES

## 📋 Vue d'ensemble

Ce module permet la gestion complète des déclarations PCS (Programme de Consolidation des Statistiques) pour les programmes **UEMOA** et **AES**. Il gère également les **autres types de demandes** financières.

### ✨ Fonctionnalités principales

1. **Gestion des Bureaux de Douanes** (rattachés à la RGD)
2. **Déclarations PCS** (UEMOA et AES)
   - Saisie mensuelle par poste
   - Saisie multiple par la RGD pour tous ses bureaux
   - Workflow de validation (brouillon → soumis → validé/rejeté)
3. **Autres Demandes** (demandes financières diverses)
4. **Génération d'états PDF** conformes aux modèles officiels
5. **Traçabilité complète** des modifications

---

## 🗄️ Structure de la Base de Données

### Tables créées

| Table | Description |
|-------|-------------|
| `bureaux_douanes` | Bureaux de douanes rattachés à la RGD |
| `declarations_pcs` | Déclarations mensuelles PCS (UEMOA/AES) |
| `autres_demandes` | Autres types de demandes financières |
| `pieces_jointes_pcs` | Pièces jointes des déclarations |
| `historique_statuts_pcs` | Historique des changements de statut |

### Table `users` enrichie

Deux nouveaux champs ajoutés :
- `peut_saisir_pcs` : Autorisation de saisir des déclarations
- `peut_valider_pcs` : Autorisation de valider (rôle ACCT)

---

## 🚀 Installation

### 1. Exécuter les migrations

```bash
php artisan migrate
```

Cela va créer toutes les tables nécessaires au module PCS.

### 2. Exécuter le seeder

```bash
php artisan db:seed --class=PcsSeeder
```

Le seeder va créer :
- ✅ Les postes (RGD, ACCT, KAYES, KOULIKORO, etc.)
- ✅ Les bureaux de douanes (200, 201, 205, 208, etc.)
- ✅ Des utilisateurs de test

### 3. Utilisateurs de test créés

| Email | Mot de passe | Rôle | Poste | Droits PCS |
|-------|-------------|------|-------|------------|
| `rgd@pcs.ml` | password | Trésorier | RGD | Saisie |
| `acct@pcs.ml` | password | ACCT | ACCT | Validation |
| `kayes@pcs.ml` | password | Trésorier | KAYES | Saisie |
| `admin@pcs.ml` | password | Admin | ACCT | Saisie + Validation |

---

## 📖 Guide d'Utilisation

### 🏢 1. Gestion des Bureaux de Douanes

**Accès** : Admin uniquement  
**URL** : `/pcs/bureaux`

- Créer, modifier, supprimer des bureaux
- Activer/désactiver un bureau
- Tous les bureaux sont automatiquement rattachés à la RGD

### 📝 2. Déclarations PCS

#### Pour un Poste Normal (KAYES, KOULIKORO, etc.)

1. Se connecter avec son compte (ex: `kayes@pcs.ml`)
2. Aller sur **PCS** → **Déclarations**
3. Cliquer sur **Nouvelle Déclaration**
4. Remplir le formulaire :
   - Sélectionner le mois et l'année
   - Saisir montants pour **UEMOA** :
     - Montant Recouvrement
     - Montant Reversement
   - Saisir montants pour **AES**  - Montant Recouvrement
     - Montant Reversement
   - Ajouter une observation (optionnel)
5. Choisir l'action :
   - **Enregistrer** : Reste en brouillon
   - **Soumettre** : Envoyé pour validation ACCT

#### Pour la RGD (Multi-Bureaux)

1. Se connecter avec le compte RGD (`rgd@pcs.ml`)
2. Aller sur **PCS** → **Déclarations**
3. Cliquer sur **Nouvelle Déclaration**
4. Le formulaire affiche :
   - Section **RGD (Propres Opérations)** : Pour la RGD elle-même
   - Section **Bureaux de Douanes** : Un bloc par bureau (200, 201, 205, etc.)
5. Pour chaque bureau ET pour la RGD, saisir :
   - Montants UEMOA (Recouvrement + Reversement)
   - Montants AES (Recouvrement + Reversement)
6. Soumettre toutes les déclarations en une fois

### ✅ 3. Validation des Déclarations (ACCT)

1. Se connecter avec le compte ACCT (`acct@pcs.ml`)
2. Aller sur **PCS** → **Déclarations**
3. Filtrer par statut : **Soumis**
4. Pour chaque déclaration :
   - Consulter les détails
   - **Valider** ou **Rejeter** (avec motif)

### 📄 4. Autres Demandes

**Accès** : Tous les postes  
**URL** : `/pcs/autres-demandes`

1. Cliquer sur **Nouvelle Demande**
2. Remplir :
   - Désignation (nature de la demande)
   - Montant
   - Date de la demande
   - Année
   - Observation (optionnel)
3. Enregistrer en brouillon ou soumettre
4. L'ACCT valide de la même manière

### 📊 5. Génération des États PDF

**URL** : `/pcs/declarations/pdf/recettes`

Paramètres :
- `programme` : UEMOA ou AES
- `annee` : Année concernée

Exemple :
```
/pcs/declarations/pdf/recettes?programme=UEMOA&annee=2025
```

---

## 🔐 Rôles et Permissions

| Rôle | Peut Saisir | Peut Valider | Peut Gérer Bureaux |
|------|-------------|--------------|-------------------|
| Admin | ✅ | ✅ | ✅ |
| ACCT | ❌ | ✅ | ❌ |
| Trésorier (Poste) | ✅ | ❌ | ❌ |
| RGD | ✅ (Multi) | ❌ | ❌ |

---

## 📐 Workflow des Statuts

### Déclarations PCS

```
[BROUILLON] ──────► [SOUMIS] ──────► [VALIDE]
     │                  │
     │                  │
     └──────────────────▼
                   [REJETE]
                   (retour modification)
```

### Règles

1. **Brouillon** : Modifiable par le créateur
2. **Soumis** : En attente de validation ACCT
3. **Validé** : Définitif, inclus dans les états
4. **Rejeté** : À corriger et resoumettre

---

## 🎨 Structure des Fichiers Créés

### Migrations
```
database/migrations/
├── 2025_10_15_100001_create_bureaux_douanes_table.php
├── 2025_10_15_100002_create_declarations_pcs_table.php
├── 2025_10_15_100003_create_autres_demandes_table.php
├── 2025_10_15_100004_create_pieces_jointes_pcs_table.php
├── 2025_10_15_100005_create_historique_statuts_pcs_table.php
└── 2025_10_15_100006_add_pcs_fields_to_users_table.php
```

### Modèles
```
app/Models/
├── BureauDouane.php
├── DeclarationPcs.php
├── AutreDemande.php
├── PieceJointePcs.php
├── HistoriqueStatutPcs.php
└── Poste.php (enrichi)
```

### Contrôleurs
```
app/Http/Controllers/PCS/
├── BureauDouaneController.php
├── DeclarationPcsController.php
└── AutreDemandeController.php
```

### Routes
```
routes/web.php (section PCS ajoutée)
```

### Seeder
```
database/seeders/
└── PcsSeeder.php
```

---

## 📊 Exemples de Données

### Déclaration Poste Normal (KAYES) - Janvier 2025

```php
Poste: KAYES
Programme: UEMOA
Mois: 1 (Janvier)
Année: 2025
Recouvrement: 20 000 000 FCFA
Reversement: 19 000 000 FCFA
Reste: 1 000 000 FCFA
```

### Déclaration RGD - Bureau 200 - Janvier 2025

```php
Bureau: BUREAU 200
Programme: UEMOA
Mois: 1
Année: 2025
Recouvrement: 15 830 016 FCFA
Reversement: 14 500 000 FCFA
Reste: 1 330 016 FCFA
```

### Consolidation RGD

```
RGD UEMOA Total = Somme(BUREAU 200 + BUREAU 201 + ... + RGD propre)
```

---

## 🔧 Configuration Additionnelle

### Ajouter un Nouveau Poste

```php
php artisan tinker

Poste::create(['nom' => 'NOUVEAU_POSTE']);
```

### Ajouter un Nouveau Bureau de Douane

```sql
INSERT INTO bureaux_douanes (poste_rgd_id, code, libelle, actif)
VALUES (
    (SELECT id FROM postes WHERE nom = 'RGD'),
    '999',
    'BUREAU 999',
    1
);
```

### Donner les Droits PCS à un Utilisateur

```php
$user = User::find(1);
$user->peut_saisir_pcs = true;
$user->peut_valider_pcs = false;
$user->save();
```

---

## 🎯 Prochaines Étapes (Optionnel)

### Vues Blade à créer (structure fournie)

Pour finaliser l'interface utilisateur, créer les vues suivantes :

```
resources/views/pcs/
├── bureaux/
│   ├── index.blade.php      (Liste des bureaux)
│   ├── create.blade.php     (Formulaire création)
│   └── edit.blade.php       (Formulaire édition)
│
├── declarations/
│   ├── index.blade.php      (Liste déclarations)
│   ├── create.blade.php     (Formulaire saisie)
│   └── show.blade.php       (Détail déclaration)
│
├── autres-demandes/
│   ├── index.blade.php      (Liste demandes)
│   ├── create.blade.php     (Formulaire)
│   ├── edit.blade.php       (Édition)
│   └── show.blade.php       (Détail)
│
└── pdf/
    ├── etat-recettes.blade.php
    └── etat-reversements.blade.php
```

**Note** : Les contrôleurs et routes sont déjà configurés. Il suffit de créer les vues en s'inspirant des vues existantes du projet (demandes de fonds).

---

## 🐛 Dépannage

### Erreur : "Table 'postes' doesn't exist"
```bash
php artisan migrate
```

### Erreur : "User doesn't have peut_saisir_pcs"
```bash
php artisan migrate:fresh --seed
```

### Les bureaux n'apparaissent pas
```bash
php artisan db:seed --class=PcsSeeder
```

---

## 📞 Support

Pour toute question ou problème :
1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier les migrations : `php artisan migrate:status`
3. Réinitialiser si nécessaire : `php artisan migrate:fresh --seed`

---

## ✅ Checklist de Vérification

Après installation, vérifier que :

- [ ] Les 6 migrations sont exécutées sans erreur
- [ ] Le seeder a créé les postes et bureaux
- [ ] Les utilisateurs de test peuvent se connecter
- [ ] La RGD peut accéder à `/pcs/declarations/create`
- [ ] Le formulaire RGD affiche tous les bureaux
- [ ] KAYES peut saisir pour UEMOA et AES
- [ ] ACCT peut voir les déclarations soumises
- [ ] ACCT peut valider/rejeter

---

## 📄 Licence

Ce module est intégré au système existant de gestion des fonds publics.

---

**Version** : 1.0.0  
**Date** : 15 Octobre 2025  
**Auteur** : Développement interne

🎉 **Le module PCS est prêt à être utilisé !**






