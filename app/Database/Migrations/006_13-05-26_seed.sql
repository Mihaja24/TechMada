
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
INSERT OR IGNORE INTO employes (nom, prenom, email, password, role, departement_id, date_embauche, actif) VALUES
('Admin', 'System', 'admin@techmada.com', '$2y$10$0FAKE.HASH.FOR.DEMO0', 'admin', 1, '2024-01-01', 1),
('Manager', 'RH', 'rh@techmada.com', '$2y$10$0FAKE.HASH.FOR.DEMO1', 'rh', 1, '2024-01-15', 1),
('Dupont', 'Jean', 'jean.dupont@techmada.com', '$2y$10$0FAKE.HASH.FOR.DEMO2', 'employe', 2, '2024-03-01', 1),
('Martin', 'Marie', 'marie.martin@techmada.com', '$2y$10$0FAKE.HASH.FOR.DEMO3', 'employe', 3, '2024-02-15', 1);
