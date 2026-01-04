-- Script SQL pour créer toutes les tables créées en 2025
-- Généré à partir des migrations Laravel
-- À exécuter dans l'ordre pour éviter les erreurs de clés étrangères

-- ============================================
-- OCTOBRE 2025
-- ============================================

-- Table: types_postes
CREATE TABLE IF NOT EXISTS `types_postes` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL,
    `libelle` varchar(255) NOT NULL,
    `niveau_hierarchique` int(11) NOT NULL,
    `peut_saisir` tinyint(1) NOT NULL DEFAULT '1',
    `peut_consolider` tinyint(1) NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `types_postes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bureaux_douanes
CREATE TABLE IF NOT EXISTS `bureaux_douanes` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `poste_rgd_id` bigint(20) UNSIGNED NOT NULL,
    `code` varchar(50) NOT NULL,
    `libelle` varchar(255) NOT NULL,
    `actif` tinyint(1) NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `bureaux_douanes_poste_rgd_id_foreign` (`poste_rgd_id`),
    CONSTRAINT `bureaux_douanes_poste_rgd_id_foreign` FOREIGN KEY (`poste_rgd_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: declarations_pcs
CREATE TABLE IF NOT EXISTS `declarations_pcs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `poste_id` bigint(20) UNSIGNED DEFAULT NULL,
    `bureau_douane_id` bigint(20) UNSIGNED DEFAULT NULL,
    `programme` enum('UEMOA','AES') NOT NULL,
    `mois` int(11) NOT NULL,
    `annee` int(11) NOT NULL,
    `montant_recouvrement` decimal(15,2) NOT NULL DEFAULT '0.00',
    `montant_reversement` decimal(15,2) NOT NULL DEFAULT '0.00',
    `reference` varchar(255) DEFAULT NULL,
    `observation` text DEFAULT NULL,
    `statut` enum('brouillon','soumis','valide','rejete') NOT NULL DEFAULT 'brouillon',
    `date_saisie` datetime NOT NULL,
    `date_soumission` datetime DEFAULT NULL,
    `date_validation` datetime DEFAULT NULL,
    `motif_rejet` text DEFAULT NULL,
    `saisi_par` bigint(20) UNSIGNED NOT NULL,
    `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_declaration` (`poste_id`, `bureau_douane_id`, `programme`, `mois`, `annee`),
    KEY `declarations_pcs_poste_id_foreign` (`poste_id`),
    KEY `declarations_pcs_bureau_douane_id_foreign` (`bureau_douane_id`),
    KEY `declarations_pcs_saisi_par_foreign` (`saisi_par`),
    KEY `declarations_pcs_valide_par_foreign` (`valide_par`),
    KEY `declarations_pcs_statut_programme_mois_annee_index` (`statut`, `programme`, `mois`, `annee`),
    CONSTRAINT `declarations_pcs_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `declarations_pcs_bureau_douane_id_foreign` FOREIGN KEY (`bureau_douane_id`) REFERENCES `bureaux_douanes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `declarations_pcs_saisi_par_foreign` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `declarations_pcs_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: autres_demandes
CREATE TABLE IF NOT EXISTS `autres_demandes` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `poste_id` bigint(20) UNSIGNED NOT NULL,
    `designation` varchar(500) NOT NULL,
    `montant` decimal(15,2) NOT NULL,
    `montant_accord` decimal(15,2) DEFAULT NULL COMMENT 'Montant effectivement accordé par l''ACCT',
    `observation` text DEFAULT NULL,
    `date_demande` date NOT NULL,
    `annee` int(11) NOT NULL,
    `statut` enum('brouillon','soumis','valide','rejete') NOT NULL DEFAULT 'brouillon',
    `date_validation` datetime DEFAULT NULL,
    `motif_rejet` text DEFAULT NULL,
    `saisi_par` bigint(20) UNSIGNED NOT NULL,
    `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `autres_demandes_poste_id_foreign` (`poste_id`),
    KEY `autres_demandes_saisi_par_foreign` (`saisi_par`),
    KEY `autres_demandes_valide_par_foreign` (`valide_par`),
    KEY `autres_demandes_poste_id_annee_date_demande_index` (`poste_id`, `annee`, `date_demande`),
    KEY `autres_demandes_statut_index` (`statut`),
    CONSTRAINT `autres_demandes_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `autres_demandes_saisi_par_foreign` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `autres_demandes_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: pieces_jointes_pcs
CREATE TABLE IF NOT EXISTS `pieces_jointes_pcs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `declaration_pcs_id` bigint(20) UNSIGNED NOT NULL,
    `nom_fichier` varchar(255) NOT NULL,
    `nom_original` varchar(255) NOT NULL,
    `chemin_fichier` varchar(500) NOT NULL,
    `type_mime` varchar(100) NOT NULL,
    `taille` int(11) NOT NULL,
    `uploaded_by` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pieces_jointes_pcs_declaration_pcs_id_foreign` (`declaration_pcs_id`),
    KEY `pieces_jointes_pcs_uploaded_by_foreign` (`uploaded_by`),
    CONSTRAINT `pieces_jointes_pcs_declaration_pcs_id_foreign` FOREIGN KEY (`declaration_pcs_id`) REFERENCES `declarations_pcs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `pieces_jointes_pcs_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: historique_statuts_pcs
CREATE TABLE IF NOT EXISTS `historique_statuts_pcs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `declaration_pcs_id` bigint(20) UNSIGNED NOT NULL,
    `ancien_statut` varchar(50) DEFAULT NULL,
    `nouveau_statut` varchar(50) NOT NULL,
    `utilisateur_id` bigint(20) UNSIGNED NOT NULL,
    `commentaire` text DEFAULT NULL,
    `date_changement` datetime NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `historique_statuts_pcs_declaration_pcs_id_foreign` (`declaration_pcs_id`),
    KEY `historique_statuts_pcs_utilisateur_id_foreign` (`utilisateur_id`),
    CONSTRAINT `historique_statuts_pcs_declaration_pcs_id_foreign` FOREIGN KEY (`declaration_pcs_id`) REFERENCES `declarations_pcs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `historique_statuts_pcs_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modification de la table users (ajout des champs PCS)
-- Note: Utiliser cette syntaxe si MySQL 8.0.19+ ou MariaDB 10.3.2+, sinon commenter et utiliser la syntaxe alternative
ALTER TABLE `users`
ADD COLUMN IF NOT EXISTS `peut_saisir_pcs` tinyint(1) NOT NULL DEFAULT '0' AFTER `poste_id`,
ADD COLUMN IF NOT EXISTS `peut_valider_pcs` tinyint(1) NOT NULL DEFAULT '0' AFTER `peut_saisir_pcs`;

-- Alternative pour versions MySQL plus anciennes (décommenter si nécessaire):
-- ALTER TABLE `users` ADD COLUMN `peut_saisir_pcs` tinyint(1) NOT NULL DEFAULT '0' AFTER `poste_id`;
-- ALTER TABLE `users` ADD COLUMN `peut_valider_pcs` tinyint(1) NOT NULL DEFAULT '0' AFTER `peut_saisir_pcs`;

-- ============================================
-- NOVEMBRE 2025
-- ============================================

-- Table: desstockages_pcs
CREATE TABLE IF NOT EXISTS `destockages_pcs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `reference_destockage` varchar(255) NOT NULL,
    `programme` enum('UEMOA','AES') NOT NULL,
    `periode_mois` int(11) NOT NULL,
    `periode_annee` int(11) NOT NULL,
    `montant_total_destocke` decimal(15,2) NOT NULL DEFAULT '0.00',
    `date_destockage` date NOT NULL,
    `observation` text DEFAULT NULL,
    `statut` enum('brouillon','valide','annule') NOT NULL DEFAULT 'brouillon',
    `cree_par` bigint(20) UNSIGNED NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `destockages_pcs_reference_destockage_unique` (`reference_destockage`),
    KEY `destockages_pcs_cree_par_foreign` (`cree_par`),
    KEY `destockages_pcs_programme_periode_mois_periode_annee_index` (`programme`, `periode_mois`, `periode_annee`),
    KEY `destockages_pcs_statut_index` (`statut`),
    KEY `destockages_pcs_date_destockage_index` (`date_destockage`),
    CONSTRAINT `destockages_pcs_cree_par_foreign` FOREIGN KEY (`cree_par`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: desstockages_pcs_postes
CREATE TABLE IF NOT EXISTS `destockages_pcs_postes` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `destockage_pcs_id` bigint(20) UNSIGNED NOT NULL,
    `poste_id` bigint(20) UNSIGNED DEFAULT NULL,
    `bureau_douane_id` bigint(20) UNSIGNED DEFAULT NULL,
    `montant_collecte` decimal(15,2) NOT NULL DEFAULT '0.00',
    `montant_destocke` decimal(15,2) NOT NULL DEFAULT '0.00',
    `solde_avant` decimal(15,2) NOT NULL DEFAULT '0.00',
    `solde_apres` decimal(15,2) NOT NULL DEFAULT '0.00',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `destockages_pcs_postes_destockage_pcs_id_foreign` (`destockage_pcs_id`),
    KEY `destockages_pcs_postes_poste_id_foreign` (`poste_id`),
    KEY `destockages_pcs_postes_bureau_douane_id_foreign` (`bureau_douane_id`),
    CONSTRAINT `destockages_pcs_postes_destockage_pcs_id_foreign` FOREIGN KEY (`destockage_pcs_id`) REFERENCES `destockages_pcs` (`id`) ON DELETE CASCADE,
    CONSTRAINT `destockages_pcs_postes_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `destockages_pcs_postes_bureau_douane_id_foreign` FOREIGN KEY (`bureau_douane_id`) REFERENCES `bureaux_douanes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: bureaux_trie
CREATE TABLE IF NOT EXISTS `bureaux_trie` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `poste_id` bigint(20) UNSIGNED NOT NULL,
    `code_bureau` varchar(255) NOT NULL,
    `nom_bureau` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `actif` tinyint(1) NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `bureaux_trie_code_bureau_unique` (`code_bureau`),
    KEY `bureaux_trie_poste_id_foreign` (`poste_id`),
    KEY `bureaux_trie_poste_id_actif_index` (`poste_id`, `actif`),
    CONSTRAINT `bureaux_trie_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cotisations_trie
-- Note: Cette table est créée avec la structure finale après toutes les modifications
CREATE TABLE IF NOT EXISTS `cotisations_trie` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `poste_id` bigint(20) UNSIGNED NOT NULL,
    `bureau_trie_id` bigint(20) UNSIGNED NOT NULL,
    `mois` int(11) NOT NULL,
    `annee` int(11) NOT NULL,
    `montant_cotisation_courante` decimal(15,2) NOT NULL DEFAULT '0.00',
    `montant_apurement` decimal(15,2) NOT NULL DEFAULT '0.00',
    `montant_total` decimal(15,2) NOT NULL,
    `detail_apurement` text DEFAULT NULL,
    `mode_paiement` enum('cheque','virement','especes','autre') DEFAULT NULL,
    `reference_paiement` varchar(255) DEFAULT NULL,
    `date_paiement` date DEFAULT NULL,
    `observation` text DEFAULT NULL,
    `statut` enum('valide') NOT NULL DEFAULT 'valide',
    `date_saisie` datetime DEFAULT NULL,
    `date_validation` datetime DEFAULT NULL,
    `saisi_par` bigint(20) UNSIGNED DEFAULT NULL,
    `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_cotisation_bureau_periode` (`bureau_trie_id`, `mois`, `annee`),
    KEY `cotisations_trie_poste_id_foreign` (`poste_id`),
    KEY `cotisations_trie_bureau_trie_id_foreign` (`bureau_trie_id`),
    KEY `cotisations_trie_saisi_par_foreign` (`saisi_par`),
    KEY `cotisations_trie_valide_par_foreign` (`valide_par`),
    KEY `cotisations_trie_statut_mois_annee_index` (`statut`, `mois`, `annee`),
    KEY `cotisations_trie_poste_id_annee_index` (`poste_id`, `annee`),
    CONSTRAINT `cotisations_trie_poste_id_foreign` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cotisations_trie_bureau_trie_id_foreign` FOREIGN KEY (`bureau_trie_id`) REFERENCES `bureaux_trie` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cotisations_trie_saisi_par_foreign` FOREIGN KEY (`saisi_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `cotisations_trie_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FIN DU SCRIPT
-- ============================================
-- Note: Le script utilise CREATE TABLE IF NOT EXISTS pour éviter les erreurs
-- si les tables existent déjà. Pour les modifications de colonnes (ALTER TABLE),
-- vérifiez manuellement si les colonnes existent avant d'exécuter.
