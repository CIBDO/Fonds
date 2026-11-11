-- ============================================================
-- Script SQL : Colonne libelle sur paiements_fnl
-- Corrige : Unknown column 'libelle' in 'INSERT INTO'
-- Migration équivalente : 2026_11_09_232210_add_libelle_to_paiements_fnl_table
-- Base : MySQL / MariaDB
-- ============================================================

ALTER TABLE `paiements_fnl`
ADD COLUMN `libelle` VARCHAR(255) NULL AFTER `poste_id`;

ALTER TABLE `paiements_fnl`
ADD INDEX `paiements_fnl_poste_periode_libelle_idx` (`poste_id`, `mois`, `annee`, `libelle`);

-- ============================================================
-- Fin du script
-- ============================================================
