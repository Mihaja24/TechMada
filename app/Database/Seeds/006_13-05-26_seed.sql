
-- ==============================================================
-- DONNÉES DE TEST (Optionnel)
-- ==============================================================

-- Insertion de départements
INSERT OR IGNORE INTO departements (nom, description) VALUES
('Ressources Humaines', 'Département RH'),
('Informatique', 'Département IT'),
('Ventes', 'Département Commercial'),
('Support', 'Support Client');

-- Insertion des types de congés
INSERT OR IGNORE INTO types_conge (libelle, jours_annuels, deductible) VALUES
('Congé Annuel', 20, 1),
('Maladie', 10, 0),
('Absence non rémunérée', 0, 1),
('Maternité', 14, 0),
('Décès', 3, 0);

-- Insertion d'employés
-- Remove existing employees then insert demo accounts
DELETE FROM employes;

INSERT INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
('Admin', 'System', 'admin@techmada.mg', '$2y$10$L3XDxSTnc8dppP4j8n6D1e2Ik9zlEfHOFdTqpiHf.z9VaOsdvaQfG', 'admin', 1, '2024-01-01', 1),
('Responsable', 'RH', 'rh@techmada.mg', '$2y$10$zQtYD/lMHmYlVJ1UKt6t.eMxmJA1EH131iM4ysCtNpIX6RCPyvNM2', 'rh', 1, '2024-01-15', 1),
('Soa', 'Rakoto', 'employe@techmada.mg', '$2y$10$yyK7Wg/8xq/4CshwmiClReoTwFzlD1UbG6qRywK/TxXLj3FDkv4du', 'employe', 2, '2024-03-01', 1);

-- Insertion des demandes de congés RH (statut en attente)
DELETE FROM conges;

INSERT INTO conges (employe_id, type_conge_id, date_debut, date_fin, nb_jours, motif, statut, commentaire_rh, traite_par) VALUES
(3, 1, '2026-05-18', '2026-05-20', 3, 'Congé personnel pour formalités administratives', 'en_attente', NULL, NULL),
(3, 2, '2026-05-28', '2026-05-29', 2, 'Repos médical recommandé', 'en_attente', NULL, NULL),
(2, 1, '2026-06-02', '2026-06-05', 4, 'Participation à une formation interne', 'en_attente', NULL, NULL);

-- Soldes de test pour permettre l'approbation RH
DELETE FROM soldes;

INSERT INTO soldes (employe_id, type_conge_id, annee, jours_attribues, jours_pris) VALUES
(3, 1, 2026, 20, 0),
(3, 2, 2026, 10, 0),
(2, 1, 2026, 20, 0);
