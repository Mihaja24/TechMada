
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
