# GUIDE D'ÉDITION DES COTISATIONS TRIE

## 📝 POSSIBILITÉ D'ÉDITION ET SUPPRESSION

Les postes peuvent maintenant **modifier** et **supprimer** leurs propres cotisations TRIE.

---

## 🔓 DROITS D'ACCÈS

### **👤 Utilisateur de Poste (ex: KITA)**
✅ Peut **modifier** les cotisations de son propre poste
✅ Peut **supprimer** les cotisations de son propre poste
❌ Ne peut **PAS** toucher aux cotisations des autres postes

### **👨‍💼 Admin / ACCT**
✅ Peut **modifier** TOUTES les cotisations
✅ Peut **supprimer** TOUTES les cotisations
✅ Accès total sans restriction

---

## ✏️ MODIFIER UNE COTISATION

### **Comment accéder ?**

#### **Option 1 : Depuis la liste**
```
/trie/cotisations
→ Cliquer sur l'icône 📝 "Modifier" (bouton jaune)
```

#### **Option 2 : Depuis le détail**
```
/trie/cotisations/{id}
→ Cliquer sur le bouton "Modifier" (jaune)
```

### **Formulaire d'édition**

Le formulaire permet de modifier :

#### **✅ Modifiable**
- 💰 **Montant Cotisation Courante**
- 💰 **Montant Apurement**
- 📝 **Détail Apurement**
- 💳 **Mode de Paiement**
- 📄 **Référence de Paiement**
- 📅 **Date de Paiement**
- 💬 **Observation**

#### **❌ Non Modifiable** (Informations fixes)
- 📅 **Période** (Mois/Année)
- 🏢 **Poste**
- 🏛️ **Bureau**

---

## 🗑️ SUPPRIMER UNE COTISATION

### **Comment accéder ?**

#### **Depuis le détail de la cotisation**
```
/trie/cotisations/{id}
→ Cliquer sur le bouton "Supprimer" (rouge)
→ Confirmer dans la modal
```

### **Modal de Confirmation**

Affiche :
- ⚠️ Avertissement : "Cette action est irréversible"
- 📊 Récapitulatif de la cotisation :
  - Période
  - Bureau
  - Montant total
- 🔴 Bouton "Confirmer la Suppression"

---

## 🔒 SÉCURITÉ

### **Vérifications Backend**

Pour chaque action (modifier/supprimer) :

```php
// Vérifier que l'utilisateur peut modifier cette cotisation
if (!in_array($user->role, ['admin', 'acct'])) {
    if ($user->poste_id != $cotisation->poste_id) {
        return Error: "Vous ne pouvez modifier que les cotisations de votre propre poste."
    }
}
```

### **Protection des Données**

- ✅ Un poste ne peut modifier QUE ses cotisations
- ✅ Impossible de modifier période/poste/bureau (intégrité)
- ✅ Confirmation requise pour suppression
- ✅ Traçabilité maintenue (date_validation, valide_par)

---

## 🎯 CAS D'USAGE

### **Cas 1 : Correction d'un Montant**
```
Problème : Montant saisi incorrect
Solution :
1. Aller sur /trie/cotisations
2. Cliquer sur "Modifier" (icône crayon)
3. Corriger le montant
4. Enregistrer
✅ Cotisation mise à jour
```

### **Cas 2 : Ajout d'une Référence Manquante**
```
Problème : Oublié de saisir la référence de paiement
Solution :
1. Ouvrir la cotisation
2. Cliquer sur "Modifier"
3. Ajouter la référence (ex: CHQ BDM n°8903232)
4. Enregistrer
✅ Référence ajoutée
```

### **Cas 3 : Suppression d'une Cotisation Erronée**
```
Problème : Cotisation créée par erreur
Solution :
1. Ouvrir la cotisation
2. Cliquer sur "Supprimer"
3. Confirmer dans la modal
✅ Cotisation supprimée
```

---

## 📊 INTERFACE UTILISATEUR

### **Page Liste (index.blade.php)**
```
Actions par ligne :
[👁️ Voir] [✏️ Modifier]
```

### **Page Détail (show.blade.php)**
```
En bas de page :
[✏️ Modifier] [🗑️ Supprimer] [← Retour]
```

### **Page Édition (edit.blade.php)**
```
Formulaire avec :
- Informations fixes (grisées)
- Champs modifiables
- Boutons : [❌ Annuler] [💾 Enregistrer]
```

---

## ⚠️ POINTS D'ATTENTION

### **Traçabilité**
✅ L'édition ne change **pas** les champs de traçabilité :
- `saisi_par` : Reste inchangé
- `valide_par` : Reste inchangé
- `date_saisie` : Reste inchangée
- `date_validation` : Reste inchangée

### **Calcul Automatique**
✅ Le `montant_total` est **recalculé automatiquement** lors de la mise à jour :
```php
montant_total = montant_cotisation_courante + montant_apurement
```

### **Suppression**
⚠️ **Irréversible** : Aucun "soft delete", suppression définitive
⚠️ **Confirmation requise** : Modal pour éviter les erreurs

---

## 🚀 AVANTAGES

### **Flexibilité**
- ✅ Corriger les erreurs de saisie
- ✅ Compléter les informations manquantes
- ✅ Supprimer les doublons ou erreurs

### **Autonomie**
- ✅ Les postes gèrent eux-mêmes leurs cotisations
- ✅ Pas besoin de contacter l'admin pour chaque correction
- ✅ Réactivité accrue

### **Contrôle**
- ✅ Historique complet dans la base de données
- ✅ Impossible de modifier les données d'autres postes
- ✅ Sécurité maintenue

---

## 📝 EXEMPLE COMPLET

### **Scénario : Correction d'une cotisation KITA**

**Situation** :
- Cotisation DIBO - Novembre 2025
- Montant saisi : 7 800 000 FCFA
- Référence : CH008
- **Erreur** : Le montant correct est 7 900 000 FCFA

**Actions** :
1. Aller sur `/trie/cotisations`
2. Trouver la ligne "DIBO - Novembre 2025"
3. Cliquer sur l'icône ✏️ "Modifier"
4. Changer le montant : 7 800 000 → 7 900 000
5. Cliquer sur "Enregistrer les Modifications"
6. ✅ Cotisation corrigée !

**Résultat** :
- Montant mis à jour : 7 900 000 FCFA
- Montant total recalculé automatiquement
- Message : "La cotisation a été modifiée avec succès."

---

**Les postes ont maintenant un contrôle total sur leurs cotisations !** 🎉

