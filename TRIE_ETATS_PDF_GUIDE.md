# GUIDE DES ÉTATS PDF TRIE

## 📊 DEUX TYPES D'ÉTATS DISPONIBLES

Le module TRIE propose deux états PDF différents correspondant aux besoins de reporting.

---

## 📄 ÉTAT 1 : SITUATION MENSUELLE DES PAIEMENTS

### **Description**
État des paiements TRIE/CCIM pour **un mois donné**, regroupé par **POSTE**.

### **Format du Document**

#### **En-tête Officiel**
```
MINISTERE DE L'ECONOMIE          REPUBLIQUE DU MALI
ET DES FINANCES                  Un Peuple - Un But - Une Foi
DIRECTION GENERALE DU TRESOR
ET DE LA COMPTABILITE PUBLIQUE
```

#### **Titre**
```
SITUATION DES PAIEMENTS CFG-TRIE / CCIM
DU MOIS DE [MOIS] [ANNÉE]
```

#### **Tableau des Données**

| Postes | Recouvrements du mois courant | Apurement solde antérieur | Montants Payés | Réf. Paiement | Observations |
|--------|-------------------------------|---------------------------|----------------|---------------|--------------|
| KAYES | 212 169 769 | - | 212 169 769 | CHQ BDM n°8903232 du 14/04/2025 | |
| KOULIKORO | 12 438 507 | - | 12 438 507 | | |
| SIKASSO | 32 166 338 | 5 000 000 | 37 166 338 | Chq BCEAO n°0393899 du 22/04/25 | |
| **TOTAL** | **335 255 589** | **5 000 000** | **340 255 589** | | |

### **Données Affichées**
- ✅ **Recouvrements du mois courant** : Somme des cotisations courantes du mois
- ✅ **Apurement** : Somme des montants de rattrapage
- ✅ **Montants Payés** : Total (recouvrement + apurement)
- ✅ **Références** : Agrégation des références de paiement
- ✅ **Observations** : Agrégation des observations

### **Agrégation**
- Les données de **tous les bureaux d'un poste** sont **additionnées**
- Une seule ligne par poste
- Références de paiement concaténées

### **Comment Générer ?**

1. Accéder à `/trie/etats`
2. Section "État Mensuel des Paiements"
3. Sélectionner :
   - **Mois** (ex: Février)
   - **Année** (ex: 2025)
4. Cliquer sur "Générer l'État Mensuel"
5. ✅ PDF téléchargé : `Situation_Paiements_TRIE_CCIM_Fevrier_2025.pdf`

---

## 📊 ÉTAT 2 : SITUATION CONSOLIDÉE ANNUELLE

### **Description**
État consolidé des cotisations pour **une année complète**, détaillé par **BUREAU** et par **MOIS**.

### **Format du Document**

#### **Titre**
```
SITUATION DES COTISATIONS AU FONDS DE GARANTIE TRIE p/c CCIM [ANNÉE]
```

#### **Tableau Principal - Détail Mensuel**

| POSTE / Mois | TR KAYES |  | TR NIORO | TR SIKASSO |  | ... | TOTAL |
|--------------|----------|----------|----------|------------|----------|-----|-------|
|              | Diboli | Mahinamine | Gogui | Zégoua | Heremakono | ... |       |
| Janvier 2024 | 134 300 661 | 98 231 584 | ... | ... | ... | ... | XXX |
| Février 2024 | ... | ... | ... | ... | ... | ... | XXX |
| ... | ... | ... | ... | ... | ... | ... | XXX |
| **TOTAL** | **XXX** | **XXX** | **XXX** | **XXX** | **XXX** | ... | **XXX** |

#### **Tableau Récapitulatif Bi-Annuel**

| DESIGNATION | 2023 | 2024 | TOTAL |
|-------------|------|------|-------|
| KAYES | 2 184 532 245 | 2 448 275 363 | 4 632 807 608 |
| NIORO | ... | ... | ... |
| SIKASSO | ... | ... | ... |
| **TOTAL** | **XXX** | **XXX** | **XXX** |

### **Données Affichées**
- ✅ **Détail mensuel** : Montant total par bureau et par mois
- ✅ **Total par bureau** : Somme des 12 mois
- ✅ **Total par mois** : Somme de tous les bureaux
- ✅ **Comparatif annuel** : Année N-1 vs Année N

### **Comment Générer ?**

1. Accéder à `/trie/etats`
2. Section "État Consolidé Annuel"
3. Sélectionner :
   - **Année** (ex: 2024)
4. Cliquer sur "Générer l'État Consolidé"
5. ✅ PDF téléchargé : `Situation_Cotisations_TRIE_CCIM_2024.pdf`

---

## 🎯 DIFFÉRENCES ENTRE LES DEUX ÉTATS

| Critère | État Mensuel | État Consolidé |
|---------|--------------|----------------|
| **Période** | 1 mois | 1 année complète |
| **Niveau détail** | Par POSTE | Par BUREAU |
| **Lignes** | 1 par poste | 12 (mois) |
| **Colonnes** | Fixes (6) | Dynamiques (bureaux) |
| **Tableau récap** | Non | Oui (bi-annuel) |
| **Format** | Simple | Complexe |
| **Usage** | Rapport mensuel | Analyse annuelle |

---

## 🔧 IMPLÉMENTATION TECHNIQUE

### **Contrôleur : `EtatTrieController`**

#### **Méthode `etatMensuel()`**
```php
1. Récupérer les cotisations du mois
2. Grouper par POSTE (somme des bureaux)
3. Agréger les références et observations
4. Calculer les totaux
5. Générer le PDF
```

#### **Méthode `etatConsolide()`**
```php
1. Récupérer les cotisations de l'année
2. Organiser par POSTE → BUREAU → MOIS
3. Calculer totaux mensuels et annuels
4. Récupérer données année N-1
5. Générer le PDF avec 2 tableaux
```

### **Vues PDF**

#### **`etat-mensuel.blade.php`**
- En-tête officiel (Ministère + République)
- Titre formaté
- Référence circulaire
- Tableau 6 colonnes
- Section signature

#### **`etat-consolide.blade.php`**
- Titre centré
- Tableau dynamique (colonnes = bureaux)
- Lignes mensuelles (Janvier à Décembre)
- Tableau récapitulatif bi-annuel
- Format paysage (landscape)

---

## 📋 ROUTES DISPONIBLES

```php
GET /trie/etats             → Page des états (formulaires)
GET /trie/etats/mensuel     → Générer PDF mensuel (ACCT/Admin)
GET /trie/etats/consolide   → Générer PDF consolidé (ACCT/Admin)
```

---

## 💡 CAS D'USAGE

### **Scénario 1 : Rapport Mensuel pour ACCT**
```
Besoin : Rapport officiel de février 2025
Actions :
1. /trie/etats
2. Sélectionner : Février 2025
3. Générer l'État Mensuel
4. ✅ PDF avec totaux par poste
5. Utilisation : Transmission à la hiérarchie
```

### **Scénario 2 : Analyse Annuelle**
```
Besoin : Bilan complet de l'année 2024
Actions :
1. /trie/etats
2. Sélectionner : 2024
3. Générer l'État Consolidé
4. ✅ PDF avec détail mensuel par bureau
5. Utilisation : Audit, contrôle, archives
```

### **Scénario 3 : Comparaison Bi-Annuelle**
```
Besoin : Comparer 2023 vs 2024
Actions :
1. Générer État Consolidé 2024
2. Consulter le tableau récapitulatif
3. ✅ Voir 2023, 2024 et total
4. Utilisation : Analyse d'évolution
```

---

## 📊 EXEMPLE DE DONNÉES

### **État Mensuel - Février 2025**

**Input** :
- KAYES : 2 bureaux (Diboli + Mahinamine)
  - Diboli : 150M cotisation
  - Mahinamine : 62M cotisation
- SIKASSO : 2 bureaux
  - Zégoua : 25M cotisation
  - Heremakono : 12M cotisation, 5M apurement

**Output PDF** :
```
Postes     | Recouvr. | Apurem. | Total   | Réf. Paiement
---------------------------------------------------------
KAYES      | 212M     | -       | 212M    | CHQ BDM...
SIKASSO    | 37M      | 5M      | 42M     | Chq BCEAO...
---------------------------------------------------------
TOTAL      | 249M     | 5M      | 254M    |
```

### **État Consolidé - 2024**

**Output PDF** :
```
Tableau 1 - Détail mensuel :
Mois     | Diboli | Mahinamine | Gogui | ... | TOTAL
--------------------------------------------------------
Janvier  | 134M   | 98M        | ...   | ... | XXX
Février  | 150M   | 62M        | ...   | ... | XXX
...

Tableau 2 - Récapitulatif :
Poste    | 2023        | 2024        | TOTAL
--------------------------------------------
KAYES    | 2 184 532K  | 2 448 275K  | 4 632 807K
```

---

## 🎨 PERSONNALISATION

### **Paramètres Disponibles**
- ✅ Mois (pour état mensuel)
- ✅ Année (pour les deux états)

### **Format PDF**
- ✅ **État Mensuel** : Format A4 paysage
- ✅ **État Consolidé** : Format A4 paysage
- ✅ Police : DejaVu Sans (support UTF-8)
- ✅ Marges optimisées

---

## ✅ CHECKLIST D'IMPLÉMENTATION

- [x] Contrôleur `EtatTrieController` créé
- [x] Méthode `etatMensuel()` implémentée
- [x] Méthode `etatConsolide()` implémentée
- [x] Vue PDF `etat-mensuel.blade.php` créée
- [x] Vue PDF `etat-consolide.blade.php` créée
- [x] Page des états mise à jour avec formulaires
- [x] Routes configurées
- [x] Middleware ACCT/Admin appliqué
- [x] Cache nettoyé

---

## 🚀 PRÊT À UTILISER

**Les états PDF sont maintenant opérationnels !**

Accédez à `/trie/etats` pour :
- ✅ Générer des rapports mensuels
- ✅ Générer des rapports annuels consolidés
- ✅ Télécharger en PDF
- ✅ Utilisation officielle

---

**Le module TRIE est maintenant complet avec tous les états de reporting !** 🎉

