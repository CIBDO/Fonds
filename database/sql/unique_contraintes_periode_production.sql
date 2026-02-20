-- ============================================================
-- Script SQL : Contraintes d'unicité (un enregistrement par période)
-- À exécuter en production pour éviter les doublons (mois/année)
-- Base : MySQL / MariaDB
-- ============================================================
-- Si un index existe déjà, MySQL renverra une erreur "Duplicate key name".
-- Dans ce cas, ignorer la ligne concernée ou vérifier avant avec :
--   SHOW INDEX FROM nom_table WHERE Key_name = 'nom_index';
-- ============================================================

-- 1. Cotisations TRIE : une seule cotisation par (bureau, mois, année)
ALTER TABLE `cotisations_trie`
ADD UNIQUE KEY `unique_cotisation_bureau_periode` (`bureau_trie_id`, `mois`, `annee`);

-- 2. Déclarations PCS : une seule déclaration par (poste, bureau, programme, mois, année)
ALTER TABLE `declarations_pcs`
ADD UNIQUE KEY `unique_declaration` (`poste_id`, `bureau_douane_id`, `programme`, `mois`, `annee`);

-- 3. Demandes de fonds : une seule demande par (poste, mois, année)
ALTER TABLE `demande_fonds`
ADD UNIQUE KEY `unique_demande_fonds_poste_mois_annee` (`poste_id`, `mois`, `annee`);

-- ============================================================
-- Fin du script
-- ============================================================
