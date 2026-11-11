-- ============================================================
-- Script SQL : Module VIR salaires (ACCT)
-- À exécuter en production (MySQL / MariaDB)
-- Ordre important : respecter les dépendances FK
-- ============================================================

-- 1. Référentiel banques
CREATE TABLE IF NOT EXISTS `banques` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `banques_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bordereaux de propositions de virements
CREATE TABLE IF NOT EXISTS `bordereau_virements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `demande_fonds_id` bigint unsigned NOT NULL,
  `poste_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `mois` varchar(20) NOT NULL,
  `annee` smallint unsigned NOT NULL,
  `periode_debut` date DEFAULT NULL,
  `periode_fin` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'brouillon',
  `fichier_joint` varchar(255) DEFAULT NULL,
  `soumis_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bordereau_virements_demande_fonds_id_unique` (`demande_fonds_id`),
  CONSTRAINT `bordereau_virements_demande_fonds_id_foreign`
    FOREIGN KEY (`demande_fonds_id`) REFERENCES `demande_fonds` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bordereau_virements_poste_id_foreign`
    FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bordereau_virements_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. VIR (entité capitale : n° VIR, poste, banque, mois, montant)
CREATE TABLE IF NOT EXISTS `virements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bordereau_virement_id` bigint unsigned NOT NULL,
  `poste_id` bigint unsigned NOT NULL,
  `banque_id` bigint unsigned NOT NULL,
  `numero_vir` varchar(255) NOT NULL,
  `mois` varchar(20) NOT NULL,
  `annee` smallint unsigned NOT NULL,
  `montant` decimal(18,2) NOT NULL,
  `statut_paiement` varchar(20) NOT NULL DEFAULT 'non_paye',
  `date_paiement` timestamp NULL DEFAULT NULL,
  `paye_par` bigint unsigned DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `virements_bordereau_banque_unique` (`bordereau_virement_id`, `banque_id`),
  KEY `virements_mois_annee_poste_index` (`mois`, `annee`, `poste_id`),
  KEY `virements_numero_vir_annee_index` (`numero_vir`, `annee`),
  KEY `virements_statut_paiement_index` (`statut_paiement`),
  CONSTRAINT `virements_bordereau_virement_id_foreign`
    FOREIGN KEY (`bordereau_virement_id`) REFERENCES `bordereau_virements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `virements_poste_id_foreign`
    FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `virements_banque_id_foreign`
    FOREIGN KEY (`banque_id`) REFERENCES `banques` (`id`),
  CONSTRAINT `virements_paye_par_foreign`
    FOREIGN KEY (`paye_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Lignes détail (BE / Mdt / Ord / Programme)
CREATE TABLE IF NOT EXISTS `lignes_virements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `virement_id` bigint unsigned NOT NULL,
  `num_be` varchar(255) DEFAULT NULL,
  `num_mdt` varchar(255) DEFAULT NULL,
  `ord` varchar(255) DEFAULT NULL,
  `programme` varchar(255) DEFAULT NULL,
  `creancier_libelle` varchar(255) DEFAULT NULL,
  `num_compte` varchar(255) DEFAULT 'NEANT',
  `montant` decimal(18,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lignes_virements_virement_id_foreign` (`virement_id`),
  CONSTRAINT `lignes_virements_virement_id_foreign`
    FOREIGN KEY (`virement_id`) REFERENCES `virements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Destinataires alertes e-mail
CREATE TABLE IF NOT EXISTS `destinataires_mail_alertes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  `fonds_soumise` tinyint(1) NOT NULL DEFAULT 1,
  `fonds_statut` tinyint(1) NOT NULL DEFAULT 0,
  `autre_soumise` tinyint(1) NOT NULL DEFAULT 1,
  `autre_validee` tinyint(1) NOT NULL DEFAULT 0,
  `autre_rejetee` tinyint(1) NOT NULL DEFAULT 0,
  `vir_soumission` tinyint(1) NOT NULL DEFAULT 1,
  `vir_paiement` tinyint(1) NOT NULL DEFAULT 1,
  `fnl_soumise` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `destinataires_mail_alertes_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed banques (optionnel)
-- ============================================================
INSERT INTO `banques` (`code`, `nom`, `actif`, `created_at`, `updated_at`) VALUES
('BNDA', 'BNDA - BANQUE NATIONALE DE DEVELOPPEMENT AGRICOLE', 1, NOW(), NOW()),
('BDM', 'BDM - BANQUE DE DEVELOPPEMENT DU MALI', 1, NOW(), NOW()),
('BIM', 'BIM - BANQUE INTERNATIONALE POUR LE MALI', 1, NOW(), NOW()),
('BOA', 'BOA - BANK OF AFRICA MALI', 1, NOW(), NOW()),
('Ecobank', 'ECOBANK MALI', 1, NOW(), NOW()),
('BCS', 'BCS - BANQUE COMMERCIALE DU SAHEL', 1, NOW(), NOW()),
('Orabank', 'ORABANK MALI', 1, NOW(), NOW()),
('Coris', 'CORIS BANK MALI', 1, NOW(), NOW()),
('UBA', 'UBA MALI', 1, NOW(), NOW()),
('BCI', 'BCI - BANQUE COMMERCIALE INTERNATIONALE', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `nom` = VALUES(`nom`),
  `actif` = VALUES(`actif`),
  `updated_at` = NOW();

-- ============================================================
-- Fin du script
-- ============================================================
