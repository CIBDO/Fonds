-- ============================================================
-- Script SQL : Alerte e-mail FNL (soumission paiement)
-- Corrige / ajoute la colonne fnl_soumise sur destinataires_mail_alertes
-- Migration équivalente : 2026_11_11_160000_add_fnl_soumise_to_destinataires_mail_alertes
-- Base : MySQL / MariaDB
-- ============================================================

ALTER TABLE `destinataires_mail_alertes`
ADD COLUMN `fnl_soumise` TINYINT(1) NOT NULL DEFAULT 1 AFTER `vir_paiement`;

-- ============================================================
-- Fin du script
-- ============================================================
