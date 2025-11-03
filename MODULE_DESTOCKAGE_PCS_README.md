# Module de Déstockage PCS - Documentation Complète

## Vue d'ensemble

Le module de déstockage PCS permet à l'ACCT (Agence Comptable Centrale du Trésor) de gérer la redistribution des fonds collectés depuis les postes vers les organismes concernés (UEMOA/AES).

### Caractéristique principale
**Sélection flexible** : L'ACCT peut choisir de déstocker pour un ou plusieurs postes à la fois, et non pas obligatoirement tous les postes.

---

## Structure de la Base de Données

### Table `destockages_pcs`
Stocke les informations principales de chaque opération de déstockage.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint | Identifiant unique |
| `reference_destockage` | varchar | Référence unique (ex: DST-UEMOA-01-2025-20251103-001) |
| `programme` | enum | UEMOA ou AES |
| `periode_mois` | integer | Mois concerné (1-12) |
| `periode_annee` | integer | Année concernée |
| `montant_total_destocke` | decimal(15,2) | Montant total de l'opération |
| `date_destockage` | date | Date de l'opération |
| `observation` | text | Observations éventuelles |
| `statut` | enum | brouillon, valide, annule |
| `cree_par` | bigint | ID de l'utilisateur ACCT |
| `created_at` | timestamp | Date de création |
| `updated_at` | timestamp | Date de modification |

### Table `destockages_pcs_postes`
Détail du déstockage par poste/bureau.

| Colonne | Type | Description |
|---------|------|-------------|
| `id` | bigint | Identifiant unique |
| `destockage_pcs_id` | bigint | FK vers destockages_pcs |
| `poste_id` | bigint | FK vers postes (nullable) |
| `bureau_douane_id` | bigint | FK vers bureaux_douanes (nullable) |
| `montant_collecte` | decimal(15,2) | Montant total collecté pour ce poste |
| `montant_destocke` | decimal(15,2) | Montant déstocké pour ce poste |
| `solde_avant` | decimal(15,2) | Solde avant déstockage |
| `solde_apres` | decimal(15,2) | Solde après déstockage |

**Note** : Soit `poste_id` soit `bureau_douane_id` doit être renseigné.

---

## Fichiers Créés

### 1. Migrations
- `2025_11_03_163514_create_destockages_pcs_table.php`
- `2025_11_03_163522_create_destockages_pcs_postes_table.php`

### 2. Modèles
- `app/Models/DestockagePcs.php`
- `app/Models/DestockagePcsPoste.php`

### 3. Contrôleur
- `app/Http/Controllers/PCS/DestockagePcsController.php`

### 4. Vues Blade
- `resources/views/pcs/destockages/collecte.blade.php` - Vue de collecte
- `resources/views/pcs/destockages/create.blade.php` - Création de déstockage
- `resources/views/pcs/destockages/index.blade.php` - Liste des déstockages
- `resources/views/pcs/destockages/show.blade.php` - Détail d'un déstockage
- `resources/views/pcs/destockages/etats.blade.php` - Interface de génération d'états

### 5. PDFs
- `resources/views/pcs/pdf/bordereau-destockage.blade.php` - Bordereau individuel
- `resources/views/pcs/pdf/etat-collecte-pcs.blade.php` - État de collecte annuel
- `resources/views/pcs/pdf/etat-destockages-consolide.blade.php` - État consolidé des déstockages

---

## Fonctionnalités

### A. Vue de Collecte (`/pcs/destockages/collecte`)
Affiche les fonds collectés par poste pour une période donnée.

**Affichage** :
- Montant collecté par poste
- Montant déjà déstocké
- Solde disponible
- Filtres : Programme, Mois, Année

**Actions** :
- Créer un nouveau déstockage
- Générer les états PDF

### B. Création de Déstockage (`/pcs/destockages/create`)
Formulaire interactif pour créer un déstockage.

**Workflow** :
1. Sélection du programme, mois, année, date
2. Affichage de tous les postes avec leurs soldes
3. **Sélection des postes à inclure** (checkboxes)
4. Saisie du montant à déstocker pour chaque poste sélectionné
5. Validation et enregistrement

**Validations** :
- Au moins un poste doit être sélectionné
- Montant > 0 et ≤ solde disponible
- Calcul automatique du total

**JavaScript** :
- Auto-remplissage du montant max lors de la sélection
- Calcul en temps réel du total
- Désactivation du bouton tant que la saisie est invalide

### C. Liste des Déstockages (`/pcs/destockages/index`)
Liste paginée de tous les déstockages créés.

**Filtres** :
- Programme (UEMOA/AES)
- Mois
- Année
- Statut

**Affichage** :
- Référence
- Programme
- Période
- Date
- Montant total
- Nombre de postes
- Statut

**Actions** :
- Voir le détail
- Télécharger le bordereau PDF
- Accès aux états consolidés

### D. Détail d'un Déstockage (`/pcs/destockages/{id}`)
Vue détaillée d'un déstockage spécifique.

**Affichage** :
- Informations générales
- Tableau détail par poste avec :
  - Montant collecté
  - Montant déstocké
  - Solde avant/après
- Statistiques visuelles :
  - Total collecté
  - Total déstocké
  - Taux de déstockage
  - Solde restant

### E. États et Rapports (`/pcs/destockages/etats`)
Interface de génération des états PDF.

**États disponibles** :
1. **État de Collecte** : Fonds collectés par poste et par mois
2. **État Consolidé des Déstockages** : Déstockages effectués par poste et par mois

**Statistiques rapides** :
- Collecte UEMOA/AES année en cours
- Déstocké UEMOA/AES année en cours
- Soldes disponibles
- Taux de déstockage

---

## PDFs Générés

### 1. Bordereau de Déstockage
Document officiel pour chaque opération de déstockage.

**Contenu** :
- En-tête officiel (République du Mali, Ministère, ACCT)
- Informations du déstockage (référence, programme, période, date)
- Tableau détail par poste
- Statistiques récapitulatives
- Signatures (Chef Service ACCT, Comptable en Chef)

**Format** : A4 Portrait

### 2. État de Collecte PCS
État annuel des fonds collectés.

**Contenu** :
- En-tête officiel
- Tableau par poste et par mois (Janvier à Décembre)
- Totaux : Collecté / Déstocké / Solde disponible
- Signature ACCT

**Format** : A4 Paysage

### 3. État Consolidé des Déstockages
État annuel des déstockages effectués.

**Contenu** :
- En-tête officiel
- Tableau par poste et par mois
- Récapitulatif avec ratios
- Signature ACCT

**Format** : A4 Paysage

---

## Calculs Automatiques

### Montants Collectés
```php
Montant collecté = SUM(montant_recouvrement) 
    FROM declarations_pcs 
    WHERE statut = 'valide' 
    AND programme = [programme]
    AND mois = [mois]
    AND annee = [annee]
    AND (poste_id = [poste] OR bureau_douane_id = [bureau])
```

### Montants Déjà Déstockés
```php
Montant déjà déstocké = SUM(montant_destocke) 
    FROM destockages_pcs_postes 
    JOIN destockages_pcs ON destockage_pcs_id = destockages_pcs.id
    WHERE destockages_pcs.statut = 'valide'
    AND programme = [programme]
    AND periode_mois = [mois]
    AND periode_annee = [annee]
    AND (poste_id = [poste] OR bureau_douane_id = [bureau])
```

### Solde Disponible
```php
Solde disponible = Montant collecté - Montant déjà déstocké
```

### Soldes Avant/Après Déstockage
```php
Solde avant = Montant collecté - Montant déjà déstocké (avant ce déstockage)
Solde après = Solde avant - Montant déstocké (dans ce déstockage)
```

---

## Routes Disponibles

| Route | Méthode | Contrôleur@Action | Description |
|-------|---------|-------------------|-------------|
| `/pcs/destockages` | GET | `index` | Liste des déstockages |
| `/pcs/destockages/collecte` | GET | `collecte` | Vue de collecte |
| `/pcs/destockages/etats` | GET | `etats` | Interface génération états |
| `/pcs/destockages/create` | GET | `create` | Formulaire création |
| `/pcs/destockages` | POST | `store` | Enregistrement |
| `/pcs/destockages/{id}` | GET | `show` | Détail |
| `/pcs/destockages/{id}/pdf` | GET | `pdf` | Bordereau PDF |
| `/pcs/destockages/pdf/etat-collecte` | GET | `etatCollectePdf` | État collecte PDF |
| `/pcs/destockages/pdf/etat-consolide` | GET | `etatConsolidePdf` | État consolidé PDF |

**Middleware** : `auth`, `role:admin,acct`

---

## Accès Sidebar

**Section** : PCS - ACCT > Déstockages

**Menu** :
- Vue de Collecte
- Nouveau Déstockage
- Liste des Déstockages
- États et Rapports

---

## Workflow Complet

### 1. Consultation des Fonds Collectés
1. L'ACCT accède à **Vue de Collecte**
2. Sélectionne le programme (UEMOA/AES), mois, année
3. Visualise tous les postes avec leurs soldes disponibles

### 2. Création d'un Déstockage
1. Clic sur **Nouveau Déstockage**
2. Sélection du programme, période, date de déstockage
3. **Sélection des postes à inclure** (un ou plusieurs)
4. Saisie du montant pour chaque poste
5. Validation du formulaire
6. Enregistrement automatique avec :
   - Génération de la référence unique
   - Calcul des soldes avant/après
   - Mise à jour des totaux
7. Redirection vers le détail du déstockage créé

### 3. Consultation et Export
1. Accès à la **Liste des Déstockages**
2. Filtrage si nécessaire
3. Clic sur **Voir** pour le détail
4. Téléchargement du **Bordereau PDF**

### 4. Génération des États
1. Accès à **États et Rapports**
2. Sélection du type d'état
3. Paramétrage (programme, année)
4. Téléchargement du PDF

---

## Validations et Contrôles

### Côté Frontend (JavaScript)
- Vérification que montant ≤ solde disponible
- Au moins un poste sélectionné
- Montant > 0
- Désactivation des inputs pour postes non sélectionnés
- Suppression des données de postes non sélectionnés avant soumission

### Côté Backend (PHP)
- Validation des champs requis
- Vérification du rôle (ACCT/Admin uniquement)
- Calcul et vérification des soldes
- Transaction DB pour intégrité
- Exception si montant > disponible

---

## Exemples d'Utilisation

### Cas 1 : Déstockage partiel
**Contexte** : L'ACCT veut déstocker seulement pour Kayes et Sikasso, pas pour Koulikoro.

**Actions** :
1. Accéder à "Nouveau Déstockage"
2. Sélectionner UEMOA, Janvier 2025
3. Cocher uniquement Kayes et Sikasso
4. Saisir les montants
5. Valider

**Résultat** : Déstockage créé avec 2 postes seulement. Koulikoro garde son solde intact.

### Cas 2 : Déstockage total pour un mois
**Contexte** : L'ACCT veut déstocker tous les fonds du mois de Février 2025.

**Actions** :
1. Accéder à "Nouveau Déstockage"
2. Sélectionner AES, Février 2025
3. Cocher "Tout sélectionner"
4. Les montants se remplissent automatiquement au maximum
5. Valider

**Résultat** : Déstockage créé avec tous les postes, soldes = 0.

### Cas 3 : Déstockage progressif
**Contexte** : L'ACCT veut déstocker 50% des fonds de Mars pour tous les postes.

**Actions** :
1. Accéder à "Nouveau Déstockage"
2. Sélectionner UEMOA, Mars 2025
3. Cocher "Tout sélectionner"
4. Modifier manuellement chaque montant à 50% du disponible
5. Valider

**Résultat** : Déstockage créé avec tous les postes à 50%, possibilité de refaire une opération pour les 50% restants.

---

## Formules et Indicateurs

### Taux de Déstockage
```
Taux = (Montant Déstocké / Montant Collecté) × 100
```

### Solde Disponible Global
```
Solde = Total Collecté - Total Déstocké
```

### Nombre d'Entités Déstockées
```
Nombre = COUNT(DISTINCT poste_id OR bureau_douane_id)
```

---

## Sécurité et Permissions

### Rôles Autorisés
- `acct` : Accès complet
- `admin` : Accès complet (supervision)

### Permissions Requises
- Lecture : Consultation des collectes et déstockages
- Écriture : Création de nouveaux déstockages
- Export : Génération des PDFs

---

## Intégration avec les Autres Modules

### Lien avec Déclarations PCS
- Les montants collectés proviennent de `declarations_pcs`
- Seules les déclarations **validées** sont comptabilisées
- Les montants de recouvrement alimentent la collecte

### Traçabilité
- Chaque déstockage enregistre :
  - Qui l'a créé (`cree_par`)
  - Quand (`created_at`, `date_destockage`)
  - Pour quels postes (`destockages_pcs_postes`)
  - Avec quels montants et soldes

---

## États PDF - Détails

### Bordereau de Déstockage
**Utilisation** : Document officiel pour chaque opération

**Sections** :
1. En-tête officiel (République du Mali, Ministère, ACCT)
2. Informations du déstockage
3. Tableau détail par poste
4. Statistiques
5. Observations
6. Signatures

**Génération** : Automatique après création ou via bouton dans la vue détail

### État de Collecte PCS
**Utilisation** : Rapport annuel des collectes

**Sections** :
1. En-tête officiel
2. Tableau mensuel (colonnes = mois)
3. Lignes par poste
4. Totaux : Collecté / Déstocké / Solde

**Paramètres** : Programme, Année

### État Consolidé des Déstockages
**Utilisation** : Rapport annuel des déstockages

**Sections** :
1. En-tête officiel
2. Tableau mensuel des déstockages par poste
3. Récapitulatif avec ratios
4. Signature

**Paramètres** : Programme, Année

---

## Modification du Sidebar

### Section Ajoutée
**PCS - ACCT > Déstockages** (submenu)

**Liens** :
1. Vue de Collecte - Consulter les fonds collectés
2. Nouveau Déstockage - Créer une opération
3. Liste des Déstockages - Historique
4. États et Rapports - Générer les PDFs

---

## Points Techniques Importants

### Gestion des Postes Non Sélectionnés
Le formulaire envoie TOUS les inputs (`postes[0]`, `postes[1]`, etc.) mais seuls ceux avec une checkbox cochée ont un montant > 0. Le contrôleur filtre avec :
```php
$postesSelectionnes = array_filter($validated['postes'], function($poste) {
    return isset($poste['montant_destocke']) && $poste['montant_destocke'] > 0;
});
```

### Calcul des Soldes
Les soldes sont recalculés à chaque déstockage pour prendre en compte les déstockages précédents de la même période :
```php
$dejaDestocke = DestockagePcsPoste::where('poste_id', $posteId)
    ->whereHas('destockage', function ($q) use ($programme, $mois, $annee) {
        $q->where('programme', $programme)
          ->where('periode_mois', $mois)
          ->where('periode_annee', $annee)
          ->where('statut', 'valide');
    })
    ->sum('montant_destocke');
```

### Référence Unique
Format : `DST-{PROGRAMME}-{MOIS}-{ANNÉE}-{DATE}-{NUMÉRO}`

Exemple : `DST-UEMOA-01-2025-20251103-001`

---

## Maintenance et Support

### Vérifications à Effectuer
- Les migrations ont été exécutées
- Les modèles sont bien liés (relations Eloquent)
- Les routes sont accessibles
- Le sidebar affiche les liens pour l'ACCT

### Logs et Débogage
- Les erreurs de validation sont affichées via SweetAlert
- Les transactions DB assurent la cohérence
- Les calculs sont tracés dans les colonnes `solde_avant` et `solde_apres`

---

## Évolutions Futures Possibles

1. **Statut "Brouillon"** : Permettre de sauvegarder sans valider
2. **Modification** : Permettre de modifier un déstockage avant validation
3. **Annulation** : Permettre d'annuler un déstockage avec reconstitution du solde
4. **Notifications** : Alertes aux postes concernés
5. **Historique** : Piste d'audit complète des modifications
6. **Export Excel** : États en format CSV/Excel
7. **Graphiques** : Visualisation des tendances de collecte/déstockage

---

## Support et Contact

Pour toute question ou problème concernant ce module, contacter l'équipe de développement.

**Date de création** : 03 Novembre 2025  
**Version** : 1.0  
**Auteur** : Équipe Développement ACCT

