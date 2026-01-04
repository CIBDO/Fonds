-- =========================================================
-- STRUCTURE SQL - MODULE DÉSTOCKAGE PCS
-- Date: 03/11/2025
-- Base de données: MySQL / MariaDB
-- Encodage: UTF8MB4
-- Description: Tables pour la gestion des déstockages de fonds PCS par l'ACCT
-- =========================================================

-- Table: destockages_pcs
-- Description: Opérations de déstockage de fonds PCS (UEMOA/AES)
-- Permet à l'ACCT de redistribuer les fonds collectés vers les organismes concernés
CREATE TABLE IF NOT EXISTS `destockages_pcs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference_destockage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Référence unique auto-générée (ex: DST-UEMOA-01-2025-20251103-001)',
  `programme` enum('UEMOA','AES') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Programme concerné',
  `periode_mois` int(11) NOT NULL COMMENT 'Mois concerné par le déstockage (1-12)',
  `periode_annee` int(11) NOT NULL COMMENT 'Année concernée par le déstockage',
  `montant_total_destocke` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant total de cette opération de déstockage',
  `date_destockage` date NOT NULL COMMENT 'Date de l\'opération de déstockage',
  `observation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observations ou remarques',
  `statut` enum('brouillon','valide','annule') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon' COMMENT 'Statut du déstockage',
  `cree_par` bigint(20) UNSIGNED NOT NULL COMMENT 'ID de l\'utilisateur ACCT qui a créé le déstockage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `destockages_pcs_reference_unique` (`reference_destockage`),
  KEY `destockages_pcs_programme_periode_index` (`programme`,`periode_mois`,`periode_annee`),
  KEY `destockages_pcs_statut_index` (`statut`),
  KEY `destockages_pcs_date_destockage_index` (`date_destockage`),
  KEY `destockages_pcs_cree_par_foreign` (`cree_par`),
  CONSTRAINT `destockages_pcs_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Opérations de déstockage des fonds PCS par l\'ACCT';

-- =========================================================

-- Table: destockages_pcs_postes
-- Description: Détail d'un déstockage par poste/bureau
-- Permet de tracer précisément le déstockage pour chaque entité
CREATE TABLE IF NOT EXISTS `destockages_pcs_postes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `destockage_pcs_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ID du déstockage parent',
  `poste_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID du poste (si déstockage pour un poste)',
  `bureau_douane_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID du bureau de douane (si déstockage pour un bureau RGD)',
  `montant_collecte` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant total collecté pour cette entité',
  `montant_destocke` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Montant déstocké dans cette opération',
  `solde_avant` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Solde disponible avant ce déstockage',
  `solde_apres` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Solde disponible après ce déstockage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `destockages_pcs_postes_poste_id_index` (`poste_id`),
  KEY `destockages_pcs_postes_bureau_douane_id_index` (`bureau_douane_id`),
  KEY `destockages_pcs_postes_destockage_pcs_id_index` (`destockage_pcs_id`),
  CONSTRAINT `destockages_pcs_postes_destockage_pcs_id_foreign` FOREIGN KEY (`destockage_pcs_id`) REFERENCES `destockages_pcs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `destockages_pcs_postes_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `destockages_pcs_postes_bureau_douane_id_foreign` FOREIGN KEY (`bureau_douane_id`) REFERENCES `bureaux_douanes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Détail des déstockages par poste/bureau - Soit poste_id soit bureau_douane_id doit être renseigné';

-- =========================================================
-- NOTES D'UTILISATION
-- =========================================================

/*
LOGIQUE MÉTIER:

1. COLLECTE DES FONDS
   - Les postes envoient leurs déclarations PCS (table: declarations_pcs)
   - L'ACCT collecte ces fonds (montant_recouvrement)
   - Montant collecté = SUM(montant_recouvrement) WHERE statut = 'valide'

2. DÉSTOCKAGE DES FONDS
   - L'ACCT redistribue les fonds collectés vers les organismes (UEMOA/AES)
   - L'ACCT peut choisir de déstocker pour un, plusieurs ou tous les postes
   - Chaque déstockage peut concerner plusieurs postes
   - Un poste peut faire l'objet de plusieurs déstockages (déstockage progressif)

3. CALCULS AUTOMATIQUES
   - Montant collecté (par poste) = SUM(declarations_pcs.montant_recouvrement)
   - Montant déjà déstocké = SUM(destockages_pcs_postes.montant_destocke) WHERE statut = 'valide'
   - Solde disponible = Montant collecté - Montant déjà déstocké
   - Validation: montant_destocke <= solde_disponible

4. RÉFÉRENCE UNIQUE
   - Format: DST-{PROGRAMME}-{MOIS}-{ANNÉE}-{DATE}-{NUMÉRO}
   - Exemple: DST-UEMOA-01-2025-20251103-001
   - Générée automatiquement par le modèle DestockagePcs

5. CONTRAINTES
   - Un déstockage doit avoir au moins un poste (destockages_pcs_postes)
   - Soit poste_id soit bureau_douane_id doit être renseigné (validation applicative)
   - Le montant déstocké ne peut pas dépasser le solde disponible

6. STATUTS
   - 'brouillon': Déstockage en cours de création (non utilisé actuellement)
   - 'valide': Déstockage confirmé et effectif
   - 'annule': Déstockage annulé (non utilisé actuellement)

7. RELATIONS
   - destockages_pcs.cree_par → users.id (utilisateur ACCT)
   - destockages_pcs_postes.destockage_pcs_id → destockages_pcs.id
   - destockages_pcs_postes.poste_id → postes.id (nullable)
   - destockages_pcs_postes.bureau_douane_id → bureaux_douanes.id (nullable)

EXEMPLES DE REQUÊTES:

-- Montant total collecté pour un poste/programme/période
SELECT SUM(montant_recouvrement) 
FROM declarations_pcs 
WHERE statut = 'valide' 
  AND programme = 'UEMOA' 
  AND mois = 1 
  AND annee = 2025 
  AND poste_id = 1;

-- Montant total déjà déstocké pour un poste/programme/période
SELECT SUM(dpp.montant_destocke) 
FROM destockages_pcs_postes dpp
JOIN destockages_pcs dp ON dpp.destockage_pcs_id = dp.id
WHERE dp.statut = 'valide'
  AND dp.programme = 'UEMOA'
  AND dp.periode_mois = 1
  AND dp.periode_annee = 2025
  AND dpp.poste_id = 1;

-- Solde disponible pour un poste
SELECT 
    (SELECT SUM(montant_recouvrement) FROM declarations_pcs WHERE ...) - 
    (SELECT SUM(dpp.montant_destocke) FROM destockages_pcs_postes dpp JOIN ...) 
AS solde_disponible;

-- Liste des déstockages avec détail
SELECT 
    dp.reference_destockage,
    dp.programme,
    dp.periode_mois,
    dp.periode_annee,
    dp.montant_total_destocke,
    dp.date_destockage,
    u.name as cree_par_nom,
    COUNT(dpp.id) as nb_postes
FROM destockages_pcs dp
LEFT JOIN destockages_pcs_postes dpp ON dp.id = dpp.destockage_pcs_id
LEFT JOIN users u ON dp.cree_par = u.id
WHERE dp.statut = 'valide'
GROUP BY dp.id
ORDER BY dp.periode_annee DESC, dp.periode_mois DESC, dp.created_at DESC;

-- Détail d'un déstockage avec les postes
SELECT 
    dp.reference_destockage,
    dp.programme,
    dpp.montant_destocke,
    dpp.solde_avant,
    dpp.solde_apres,
    COALESCE(p.nom, bd.libelle) as nom_entite
FROM destockages_pcs dp
JOIN destockages_pcs_postes dpp ON dp.id = dpp.destockage_pcs_id
LEFT JOIN postes p ON dpp.poste_id = p.id
LEFT JOIN bureaux_douanes bd ON dpp.bureau_douane_id = bd.id
WHERE dp.id = 1;

-- État de collecte annuel par poste et mois
SELECT 
    COALESCE(p.nom, bd.libelle) as nom_entite,
    dc.mois,
    SUM(dc.montant_recouvrement) as montant_collecte
FROM declarations_pcs dc
LEFT JOIN postes p ON dc.poste_id = p.id
LEFT JOIN bureaux_douanes bd ON dc.bureau_douane_id = bd.id
WHERE dc.statut = 'valide'
  AND dc.programme = 'UEMOA'
  AND dc.annee = 2025
GROUP BY nom_entite, dc.mois
ORDER BY nom_entite, dc.mois;

-- État consolidé des déstockages par poste et mois
SELECT 
    COALESCE(p.nom, bd.libelle) as nom_entite,
    dp.periode_mois,
    SUM(dpp.montant_destocke) as montant_destocke
FROM destockages_pcs dp
JOIN destockages_pcs_postes dpp ON dp.id = dpp.destockage_pcs_id
LEFT JOIN postes p ON dpp.poste_id = p.id
LEFT JOIN bureaux_douanes bd ON dpp.bureau_douane_id = bd.id
WHERE dp.statut = 'valide'
  AND dp.programme = 'UEMOA'
  AND dp.periode_annee = 2025
GROUP BY nom_entite, dp.periode_mois
ORDER BY nom_entite, dp.periode_mois;
*/

-- =========================================================
-- FIN DU FICHIER
-- =========================================================



