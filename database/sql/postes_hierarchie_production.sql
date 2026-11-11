-- ============================================================
-- Script SQL : Hiérarchie postes (type TR / RP)
-- Corrige : Unknown column 'type' in 'ORDER BY' sur la table postes
-- Migration équivalente : 2026_11_09_232200_add_hierarchy_fields_to_postes_table
-- Base : MySQL / MariaDB
-- ============================================================

-- 1. Colonne type (superieur | rp) — défaut = poste supérieur
ALTER TABLE `postes`
ADD COLUMN `type` VARCHAR(20) NOT NULL DEFAULT 'superieur' AFTER `nom`;

-- 2. Parent (RP rattachée à un poste supérieur)
ALTER TABLE `postes`
ADD COLUMN `parent_id` BIGINT UNSIGNED NULL AFTER `type`;

ALTER TABLE `postes`
ADD CONSTRAINT `postes_parent_id_foreign`
FOREIGN KEY (`parent_id`) REFERENCES `postes` (`id`) ON DELETE SET NULL;

-- 3. Intitulé officiel (en-têtes PDF)
ALTER TABLE `postes`
ADD COLUMN `intitule_officiel` VARCHAR(255) NULL AFTER `parent_id`;

-- 4. Région (en-têtes PDF)
ALTER TABLE `postes`
ADD COLUMN `region` VARCHAR(255) NULL AFTER `intitule_officiel`;

-- Les postes déjà présents deviennent des postes supérieurs (DEFAULT 'superieur').
-- Les RP se créent ensuite via l'écran Postes / import Excel.

-- ============================================================
-- Fin du script
-- ============================================================
