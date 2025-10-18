# Vue Consolidée - Demandes de Fonds de Salaire

## 📋 Vue d'ensemble

Cette documentation décrit la nouvelle fonctionnalité de **vue consolidée** qui permet de visualiser, filtrer et exporter toutes les demandes de fonds de salaire dans une interface unique et centralisée.

## 🎯 Objectif

Remplacer les multiples vues dispersées (situationFE, recap, detail, etc.) par une seule vue consolidée offrant :
- Tous les filtres pertinents
- Une visualisation claire des données
- Des exports en CSV et PDF
- Des totaux calculés automatiquement

## 🚀 Fonctionnalités implémentées

### 1. Filtres disponibles

La vue consolidée propose les filtres suivants :

| Filtre | Type | Description |
|--------|------|-------------|
| **Poste** | Liste déroulante | Filtrer par poste de douane |
| **Mois** | Liste déroulante | Les 12 mois de l'année en français |
| **Année** | Liste déroulante | De 5 ans en arrière à 2 ans en avant |
| **Statut** | Liste déroulante | en_attente, approuve, rejete |
| **Trésorier** | Liste déroulante | Filtrer par utilisateur trésorier |
| **Type de date** | Liste déroulante | date_envois, date_reception, created_at |
| **Date début** | Sélecteur de date | Date de début de la période |
| **Date fin** | Sélecteur de date | Date de fin de la période |

### 2. Données affichées

Le tableau consolidé affiche les colonnes suivantes :

- **ID** : Identifiant unique de la demande
- **Poste** : Nom du poste de douane
- **Trésorier** : Nom de l'utilisateur ayant créé la demande
- **Mois/Année** : Période de la demande
- **Statut** : Badge coloré (En attente/Approuvé/Rejeté)
- **Total Courant** : Salaire brut total (FCFA)
- **Montant Disponible** : Recettes douanières disponibles (FCFA)
- **Solde** : Différence entre Total Courant et Montant Disponible (FCFA)
- **Montant Envoyé** : Montant effectivement envoyé (si approuvé)
- **Date Envoi** : Date d'envoi des fonds
- **Observations** : Notes ou commentaires
- **Actions** : Bouton pour voir les détails

### 3. Totaux calculés

Quatre cartes affichent les totaux globaux (sur toutes les demandes filtrées) :

1. **Total Salaires Bruts** : Somme de tous les montants courants
2. **Total Recettes Douanières** : Somme de tous les montants disponibles
3. **Total Soldes** : Somme de tous les soldes
4. **Total Montants Envoyés** : Somme des montants envoyés (uniquement pour les demandes approuvées)

### 4. Exports

#### Export CSV
- Séparateur : point-virgule (`;`)
- Encodage : UTF-8 avec BOM (compatible Excel)
- Contenu : Toutes les colonnes + ligne de totaux
- Format des nombres : Format français (espace comme séparateur de milliers)

#### Export PDF
- Format : A4 Paysage
- Contenu : 
  - En-tête avec titre et date de génération
  - Section des filtres appliqués
  - Cartes de totaux
  - Tableau complet des demandes
  - Ligne de totaux en pied de tableau
  - Pied de page avec date et mention de confidentialité

## 📁 Fichiers créés/modifiés

### 1. Contrôleur : `app/Http/Controllers/DemandeFondsController.php`

Trois nouvelles méthodes ajoutées :

```php
// Afficher la vue consolidée avec filtres
public function consolide(Request $request)

// Exporter les données filtrées en CSV
public function consolideExportCsv(Request $request)

// Exporter les données filtrées en PDF
public function consolideExportPdf(Request $request)
```

### 2. Routes : `routes/web.php`

Trois nouvelles routes ajoutées :

```php
// Vue principale
Route::get('/demandes-fonds/consolide', [DemandeFondsController::class, 'consolide'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide');

// Export CSV
Route::get('/demandes-fonds/consolide/export-csv', [DemandeFondsController::class, 'consolideExportCsv'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide.export-csv');

// Export PDF
Route::get('/demandes-fonds/consolide/export-pdf', [DemandeFondsController::class, 'consolideExportPdf'])
    ->middleware('role:acct,admin,superviseur')
    ->name('demandes-fonds.consolide.export-pdf');
```

### 3. Vues Blade

#### Vue principale : `resources/views/demandes/consolide.blade.php`
- Formulaire de filtres complet
- Cartes de totaux
- Tableau responsive avec pagination
- Boutons d'export
- Tooltips Bootstrap pour les observations longues
- Scripts JavaScript pour les exports

#### Vue PDF : `resources/views/demandes/consolide_pdf.blade.php`
- Template optimisé pour l'impression
- Format A4 Paysage
- Styles intégrés
- Affichage conditionnel des filtres appliqués

## 🔐 Contrôle d'accès

**Rôles autorisés** : `admin`, `acct`, `superviseur`

Les trésoriers n'ont pas accès à cette vue consolidée pour des raisons de confidentialité.

## 🛠️ Utilisation

### Accéder à la vue consolidée

1. Se connecter avec un compte `admin`, `acct` ou `superviseur`
2. Naviguer vers : `/demandes-fonds/consolide`

### Filtrer les demandes

1. Sélectionner les filtres souhaités dans le formulaire en haut de page
2. Cliquer sur le bouton **"Filtrer"**
3. Les résultats et totaux se mettent à jour automatiquement

### Réinitialiser les filtres

Cliquer sur le bouton **"Réinitialiser"** en haut à droite du formulaire

### Exporter les données

#### Export CSV
1. Appliquer les filtres souhaités (optionnel)
2. Cliquer sur le bouton **"Exporter CSV"**
3. Le fichier se télécharge automatiquement : `demandes_consolidees_YYYY-MM-DD_HHMMSS.csv`

#### Export PDF
1. Appliquer les filtres souhaités (optionnel)
2. Cliquer sur le bouton **"Exporter PDF"**
3. Le fichier se télécharge automatiquement : `demandes_consolidees_YYYY-MM-DD_HHMMSS.pdf`

**Note** : Les exports contiennent toutes les demandes correspondant aux filtres appliqués, pas seulement la page actuelle.

## 🎨 Design et UX

### Couleurs des badges de statut
- **En attente** : Badge jaune (`bg-warning`)
- **Approuvé** : Badge vert (`bg-success`)
- **Rejeté** : Badge rouge (`bg-danger`)

### Cartes de totaux
- **Total Salaires Bruts** : Bleu clair (`bg-info`)
- **Total Recettes Douanières** : Vert (`bg-success`)
- **Total Soldes** : Jaune (`bg-warning`)
- **Total Montants Envoyés** : Bleu (`bg-primary`)

### Responsive
- Tableau avec défilement horizontal sur petits écrans
- Formulaire de filtres adaptatif
- Pagination centrée

## 📊 Exemple de filtrage avancé

**Cas d'usage** : Récupérer toutes les demandes approuvées du poste de Djibouti pour l'année 2024

1. **Poste** : Sélectionner "Djibouti"
2. **Année** : Sélectionner "2024"
3. **Statut** : Sélectionner "Approuvé"
4. Cliquer sur **"Filtrer"**

Les totaux affichés correspondent uniquement aux demandes filtrées.

## 🔧 Maintenance et évolution

### Ajouter un nouveau filtre

1. Ajouter le champ dans le formulaire de `consolide.blade.php`
2. Ajouter la logique de filtrage dans la méthode `consolide()` du contrôleur
3. Répliquer la logique dans `consolideExportCsv()` et `consolideExportPdf()`

### Ajouter une colonne au tableau

1. Ajouter la colonne dans `consolide.blade.php`
2. Ajouter la colonne dans `consolide_pdf.blade.php`
3. Ajouter la colonne dans l'export CSV (méthode `consolideExportCsv()`)

## 📝 Notes techniques

### Performance
- La pagination limite à 21 résultats par page
- Les totaux sont calculés sur l'ensemble des résultats filtrés (pas seulement la page)
- Les exports peuvent contenir un grand nombre de lignes

### Formats de dates
- Affichage : `dd/mm/yyyy`
- Export CSV : `dd/mm/yyyy`
- Noms de fichiers : `YYYY-MM-DD_HHMMSS`

### Encodage
- CSV : UTF-8 avec BOM pour compatibilité Excel
- PDF : UTF-8

## ✅ Tests recommandés

1. Tester chaque filtre individuellement
2. Tester plusieurs filtres combinés
3. Tester l'export CSV avec différents filtres
4. Tester l'export PDF avec différents filtres
5. Vérifier les totaux calculés
6. Tester avec un jeu de données vide
7. Tester la pagination
8. Tester les tooltips sur les observations
9. Vérifier l'accès par rôle (doit refuser l'accès aux trésoriers)
10. Tester la réinitialisation des filtres

## 🐛 Dépannage

### Les exports sont vides
- Vérifier que des demandes correspondent aux filtres appliqués
- Vérifier les permissions du dossier de téléchargement

### Les totaux ne sont pas corrects
- Vérifier que les filtres sont bien appliqués dans les trois méthodes du contrôleur
- Vérifier la logique de calcul dans le modèle `DemandeFonds`

### L'accès est refusé
- Vérifier que l'utilisateur a le rôle `admin`, `acct` ou `superviseur`
- Vérifier les middlewares dans `routes/web.php`

## 📞 Support

Pour toute question ou problème, contacter l'équipe de développement.

---

**Document créé le** : {{ date('d/m/Y') }}  
**Version** : 1.0

