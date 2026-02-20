-- ============================================================
-- Script SQL : Ajout de la colonne preuve_paiement (preuves de paiement)
-- À exécuter en production pour PCS (autres demandes, déclarations) et TRIE (cotisations)
-- Base : MySQL / MariaDB
-- ============================================================

-- 1. Table autres_demandes (PCS - Autres demandes financières)
ALTER TABLE `autres_demandes`
ADD COLUMN `preuve_paiement` VARCHAR(500) NULL AFTER `observation`;

-- 2. Table cotisations_trie (TRIE - Cotisations)
ALTER TABLE `cotisations_trie`
ADD COLUMN `preuve_paiement` VARCHAR(500) NULL AFTER `reference_paiement`;

-- 3. Table declarations_pcs (PCS - Déclarations)
ALTER TABLE `declarations_pcs`
ADD COLUMN `preuve_paiement` VARCHAR(500) NULL AFTER `reference`;

-- ============================================================
-- Fin du script
-- ============================================================
