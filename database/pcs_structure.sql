-- =========================================================
-- STRUCTURE SQL - MODULE PCS (Programme de Consolidation Statistiques)
-- Date: 15/10/2025
-- Base de données: MySQL / MariaDB
-- Encodage: UTF8MB4
-- =========================================================

-- Table: bureaux_douanes
-- Description: Bureaux de douanes rattachés à la RGD
CREATE TABLE IF NOT EXISTS `bureaux_douanes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poste_rgd_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bureaux_douanes_poste_rgd_id_index` (`poste_rgd_id`),
  CONSTRAINT `bureaux_douanes_poste_rgd_id_foreign` FOREIGN KEY (`poste_rgd_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================

-- Table: declarations_pcs
-- Description: Déclarations mensuelles PCS (UEMOA et AES)
CREATE TABLE IF NOT EXISTS `declarations_pcs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poste_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bureau_douane_id` bigint(20) UNSIGNED DEFAULT NULL,
  `programme` enum('UEMOA','AES') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mois` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `montant_recouvrement` decimal(15,2) NOT NULL,
  `montant_reversement` decimal(15,2) NOT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('brouillon','soumis','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `date_saisie` datetime NOT NULL,
  `date_soumission` datetime DEFAULT NULL,
  `date_validation` datetime DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saisi_par` bigint(20) UNSIGNED NOT NULL,
  `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_declaration` (`poste_id`,`bureau_douane_id`,`programme`,`mois`,`annee`),
  KEY `declarations_pcs_poste_id_foreign` (`poste_id`),
  KEY `declarations_pcs_bureau_douane_id_foreign` (`bureau_douane_id`),
  KEY `declarations_pcs_saisi_par_foreign` (`saisi_par`),
  KEY `declarations_pcs_valide_par_foreign` (`valide_par`),
  KEY `declarations_pcs_statut_programme_mois_annee_index` (`statut`,`programme`,`mois`,`annee`),
  CONSTRAINT `declarations_pcs_bureau_douane_id_foreign` FOREIGN KEY (`bureau_douane_id`) REFERENCES `bureaux_douanes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `declarations_pcs_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `declarations_pcs_saisi_par_foreign` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `declarations_pcs_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================

-- Table: autres_demandes
-- Description: Autres types de demandes financières
CREATE TABLE IF NOT EXISTS `autres_demandes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poste_id` bigint(20) UNSIGNED NOT NULL,
  `designation` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` decimal(15,2) NOT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_demande` date NOT NULL,
  `annee` int(11) NOT NULL,
  `statut` enum('brouillon','soumis','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'brouillon',
  `date_validation` datetime DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `saisi_par` bigint(20) UNSIGNED NOT NULL,
  `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `autres_demandes_poste_id_foreign` (`poste_id`),
  KEY `autres_demandes_saisi_par_foreign` (`saisi_par`),
  KEY `autres_demandes_valide_par_foreign` (`valide_par`),
  KEY `autres_demandes_poste_id_annee_date_demande_index` (`poste_id`,`annee`,`date_demande`),
  KEY `autres_demandes_statut_index` (`statut`),
  CONSTRAINT `autres_demandes_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `autres_demandes_saisi_par_foreign` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `autres_demandes_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================

-- Table: pieces_jointes_pcs
-- Description: Pièces jointes des déclarations PCS
CREATE TABLE IF NOT EXISTS `pieces_jointes_pcs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `declaration_pcs_id` bigint(20) UNSIGNED NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_mime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille` int(11) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pieces_jointes_pcs_declaration_pcs_id_index` (`declaration_pcs_id`),
  KEY `pieces_jointes_pcs_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `pieces_jointes_pcs_declaration_pcs_id_foreign` FOREIGN KEY (`declaration_pcs_id`) REFERENCES `declarations_pcs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pieces_jointes_pcs_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================

-- Table: historique_statuts_pcs
-- Description: Historique des changements de statut des déclarations
CREATE TABLE IF NOT EXISTS `historique_statuts_pcs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `declaration_pcs_id` bigint(20) UNSIGNED NOT NULL,
  `ancien_statut` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nouveau_statut` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `utilisateur_id` bigint(20) UNSIGNED NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_changement` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `historique_statuts_pcs_declaration_pcs_id_index` (`declaration_pcs_id`),
  KEY `historique_statuts_pcs_utilisateur_id_foreign` (`utilisateur_id`),
  CONSTRAINT `historique_statuts_pcs_declaration_pcs_id_foreign` FOREIGN KEY (`declaration_pcs_id`) REFERENCES `declarations_pcs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historique_statuts_pcs_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================

-- Modification de la table users
-- Description: Ajout des champs pour les droits PCS
ALTER TABLE `users`
ADD COLUMN `peut_saisir_pcs` tinyint(1) NOT NULL DEFAULT 0 AFTER `poste_id`,
ADD COLUMN `peut_valider_pcs` tinyint(1) NOT NULL DEFAULT 0 AFTER `peut_saisir_pcs`;

-- =========================================================
-- FIN DU SCRIPT
-- =========================================================

-- NOTES D'UTILISATION:
-- 1. Exécuter ce script après l'installation de Laravel et la création de la table 'postes'
-- 2. La table 'postes' doit déjà exister dans la base de données
-- 3. La table 'users' doit déjà exister dans la base de données
-- 4. Tous les index et clés étrangères sont créés automatiquement
-- 5. L'encodage UTF8MB4 est requis pour supporter tous les caractères

